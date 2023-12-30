<?php

namespace App\Core;

use Exception;
use PharData;
use VulcanPhp\EasyCurl\EasyCurl;
use VulcanPhp\FileSystem\File;
use VulcanPhp\FileSystem\Folder;
use VulcanPhp\FileSystem\Handler\FileHandler;
use VulcanPhp\FileSystem\Handler\FolderHandler;
use ZipArchive;

class UpdateManager
{
    public static function check(): void
    {
        $result = EasyCurl::get('https://github.com/vulcanphp/coplay/releases/latest');

        if ($result->getStatus() == 200) {
            $version = substr($result->lastUrl(), strrpos($result->lastUrl(), '/') + 1);

            // set last checked version information
            Configurator::$instance->set('update', [
                'checked'   => time(),
                'version'   => $version,
                'download'  => "https://github.com/vulcanphp/coplay/archive/refs/tags/{$version}.tar.gz"
            ]);
        }
    }

    public static function download(): void
    {
        if (Configurator::$instance->has('update')) {
            $manager    = new UpdateManager;
            $update     = Configurator::$instance->get('update');
            $download   = storage()->uploadFromUrl($update['download'], true);
            $filepath   = sys_get_temp_dir() . '/coplay-update.tar.gz';

            // move download file 
            File::move($download, $filepath);

            if (!empty($filepath)) {
                // Take Backup of app configuration files
                $manager->takeBackup($update);

                // replace application files with updated zip
                $phar = new PharData($filepath);

                $phar->extractTo(root_dir());

                $folder = root_dir(
                    $phar
                        ->current()
                        ->getFileName()
                );

                $manager->moveFolder($folder, root_dir());

                unset($phar);

                // Restore app configuration files
                $manager->restoreBackup($update);

                // Post Update
                $manager->postUpdate($update);

                // Remove Current Folder
                Folder::remove($folder);

                // Remove Zip file
                unlink($filepath);
            }
        }
    }

    protected function moveFolder($from, $to)
    {
        foreach (Folder::scan($from) as $resource) {
            if ($resource instanceof FolderHandler) {
                $this->moveFolder($resource->getPath(), str_replace($from, $to, $resource->getPath()));
            }

            if ($resource instanceof FileHandler) {
                $resource->move(str_replace($from, $to, $resource->getPath()));
            }
        }
    }

    protected function takeBackup(): void
    {
        $zipName    = 'um_backup.zip';
        $zip        = new ZipArchive;

        if ($zip->open($zipName, ZipArchive::CREATE) !== true) {
            throw new Exception('Failed to open Zip File');
        }

        foreach ([
            'config/app.php',
            'config/database.php',
            'storage/cooplay.json',
        ] as $file) {
            $zip->addFile(root_dir($file), $file);
        }

        $zip->close();

        if (file_exists(sys_get_temp_dir() . '/' . $zipName)) {
            unlink(sys_get_temp_dir() . '/' . $zipName);
        }

        copy($zipName, sys_get_temp_dir() . '/' . $zipName);

        unlink($zipName);
    }

    protected function restoreBackup(): void
    {
        $zip = new ZipArchive;

        if ($zip->open(sys_get_temp_dir() . '/' . 'um_backup.zip') !== true) {
            throw new Exception('Failed to open Zip file');
        }

        $zip->extractTo(root_dir());

        $zip->close();
    }

    protected function postUpdate($update): void
    {
        // update version number
        file_put_contents(
            root_dir('config/app.php'),
            str_ireplace(
                ["'version' => '" . config('app.version') . "'", '"version" => "' . config('app.version') . '"'],
                ["'version' => '" . $update['version'] . "'", '"version" => "' . $update['version'] . '"'],
                file_get_contents(root_dir('config/app.php'))
            )
        );

        // update coplay.json
        Configurator::$instance->set('remove-donate', false);

        // reload config/app.php file
        config('app', null, true);

        // remove update from coplay.json
        Configurator::$instance->remove('update');

        // remove config backup.zip
        unlink(sys_get_temp_dir() . '/' . 'um_backup.zip');
    }
}
