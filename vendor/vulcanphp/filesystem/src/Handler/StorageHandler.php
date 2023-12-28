<?php

namespace VulcanPhp\FileSystem\Handler;

use VulcanPhp\FileSystem\Exceptions\StorageException;
use VulcanPhp\FileSystem\Interfaces\IStorageHandler;
use ZipArchive;

class StorageHandler extends FolderHandler implements IStorageHandler
{
    protected array $config = [
        'upload_extensions' => null,
        'max_upload_size'   => null, // 1048576 = 1mb
        'upload_dir'        => null
    ];

    public function __construct(string $folderPath, array $config = [])
    {
        if (!is_dir($folderPath)) {
            throw new StorageException('Invalid Storage Directory');
        }

        $this->setPath($folderPath);

        $this->config = array_merge($this->config,  $config);
    }

    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function setConfig(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    public function download(string $fileName): void
    {
        $file = $this->getDownloadFile($fileName);

        $this->downloadHeaders($file);

        flush();

        $file->readFile();

        exit;
    }

    // set the download rate limit (20.5 => 20,5 kb/s)
    public function downloadRate(string $fileName, float $rate): void
    {
        $file = $this->getDownloadFile($fileName);

        $this->downloadHeaders($file);

        flush();

        $resource = $file->open('r');

        while (!$file->end($resource)) {
            print $file->read($resource, round($rate * 1024));

            flush();

            sleep(1);
        }

        $file->close($resource);

        exit;
    }

    public function downloadZip($files, string $zipName): void
    {
        if (!class_exists('ZipArchive')) {
            throw new StorageException('Zip: needs to be enabled');
        }

        $zip = new ZipArchive;

        if ($zip->open($zipName, ZipArchive::CREATE) !== true) {
            throw new StorageException('Failed to open Zip File');
        }

        foreach ((array) $files as $file) {
            $file = $this->getFile($file);

            if ($file->exists()) {
                $zip->addFile($file->getPath(), $file->getName());
            }
        }

        $zip->close();

        foreach ([
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename=' . $zipName,
            'Content-Length'      => filesize($zipName),
        ] as $key => $value) {
            header("$key: $value");
        }

        flush();

        readfile($zipName);

        unlink($zipName);

        exit;
    }

    protected function downloadHeaders(FileHandler $file): self
    {
        foreach ([
            'Content-Description' => 'File Transfer',
            'Content-Type'        => $file->getMimeType(),
            'Content-Length'      => $file->getSize(),
            'Content-Disposition' => 'attachment; filename=' . $file->getName(),
            'Expires'             => 0,
            'Cache-Control'       => 'must-revalidate',
            'Pragma'              => 'public',
        ] as $key => $value) {
            header("$key: $value");
        }

        return $this;
    }

    protected function getDownloadFile(string $fileName): FileHandler
    {
        $file = $this->getFile($fileName);

        if (!$file->exists()) {
            throw new StorageException('Download File: ' . $file->getPath() . ' does not exists');
        }

        return $file;
    }

    public function getUploadPath(string $fileName = ''): string
    {
        // set upload directory
        $path = $this->getPath()
            . (!empty($this->getConfig('upload_dir')) ? DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, trim($this->getConfig('upload_dir'), '/')) : '')
            . (!empty($fileName) ? DIRECTORY_SEPARATOR . str_replace(['/', ' '], [DIRECTORY_SEPARATOR, '-'], trim($fileName, '/')) : '');

        // check upload directory
        if (!is_dir(dirname($path)) && !mkdir(dirname($path), 0777, true)) {
            throw new StorageException('Failed to Create Upload Directory: ' . dirname($path));
        }

        return $path;
    }

    public function uploadFile(array $file, string $mode = 'keep'): string
    {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        if (!empty($this->getConfig('upload_extensions')) && !in_array($extension, (array) $this->getConfig('upload_extensions'))) {
            throw new StorageException('Unsupported file extensions, supported is: ' . join(', ', (array) $this->getConfig('upload_extensions')));
        }

        if (!empty($this->getConfig('max_upload_size')) && $file['size'] > $this->getConfig('max_upload_size')) {
            throw new StorageException('File size should be <= ' . round($this->getConfig('max_upload_size') / 1048576, 4) . ' MB');
        }

        $uploadPath = $this->getUploadPath($file['name']);
        $tried      = 1;

        if ($mode === 'strict' && file_exists($uploadPath)) {
            throw new StorageException('File: ' . $uploadPath . ' already exists');
        } elseif ($mode === 'override' && file_exists($uploadPath)) {
            unlink($uploadPath);
        }

        do {
			if (file_exists($uploadPath)) {
                $uploadPath = $this->getUploadPath(
                    sprintf('%s-%s.%s', pathinfo($file['name'], PATHINFO_FILENAME), ++$tried, $extension)
                );
            }
        } while (file_exists($uploadPath));

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new StorageException('Failed to Upload: ' . $uploadPath);
        }

        return $uploadPath;
    }

    public function upload(string $index, ...$args): array
    {
        $savedFiles  = [];
        $uploadFiles = $_FILES[$index] ?? [];

        if (isset($uploadFiles['name']) && !empty($uploadFiles['name']) && is_array($uploadFiles['name'])) {
            $uploadFiles = array_map(
                fn ($name, $full_path, $type, $tmp_name, $error, $size) => [
                    'name'      => $name,
                    'full_path' => $full_path,
                    'type'      => $type,
                    'tmp_name'  => $tmp_name,
                    'error'     => $error,
                    'size'      => $size,
                ],
                $uploadFiles['name'],
                $uploadFiles['full_path'],
                $uploadFiles['type'],
                $uploadFiles['tmp_name'],
                $uploadFiles['error'],
                $uploadFiles['size'],
            );
        } else {
            $uploadFiles = [$uploadFiles];
        }

        foreach ($uploadFiles as $file) {
            if (($file['size'] ?? 0) > 1) {
                $savedFiles[] = $this->uploadFile($file, ...$args);
            }
        }

        return $savedFiles;
    }

    public function uploadFromUrl(string $url, bool $override = false): string
    {
        $location = $this->getUploadPath(basename($url));

        if ($override === false && file_exists($location)) {
            throw new StorageException('File: ' . $location . ' already exists');
        } elseif ($override === true && file_exists($location)) {
            unlink($location);
        }

        if (!extension_loaded('curl')) {
            throw new StorageException('Extension: Curl is required to download files');
        }

        $curl = curl_init();
        $download = fopen($location, 'w+');

        curl_setopt_array($curl, [
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING       => 'utf-8',
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_URL            => $url,
            CURLOPT_FILE           => $download,
        ]);

        curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        fclose($download);

        curl_close($curl);

        if ($status != 200) {
            throw new StorageException('Failed to download file from: ' . $url);
        }

        return $location;
    }
}
