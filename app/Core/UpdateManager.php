<?php

namespace App\Core;

use Exception;
use VulcanPhp\EasyCurl\EasyCurl;
use ZipArchive;

class UpdateManager
{
    public static function check(): void
    {
        $result = EasyCurl::get('https://github.com/vulcanphp/fastcache/releases/latest');

        if ($result->getStatus() == 200) {
            $version = substr($result->lastUrl(), strrpos($result->lastUrl(), '/') + 1);

            // set last checked version information
            Configurator::$instance->set('update', [
                'checked'   => time(),
                'version'   => $version,
                'download'  => "https://github.com/vulcanphp/fastcache/archive/refs/tags/{$version}.zip"
            ]);
        }
    }

    public static function download(): void
    {
        if (Configurator::$instance->has('update')) {
            $manager    = new UpdateManager;
            $update     = Configurator::$instance->get('update');
            $filepath   = storage()->uploadFromUrl($update['download']);

            if (!empty($filepath)) {
                // Take Backup of app configuration files
                $manager->takeBackup($update);

                // replace application files with updated zip
                $zip = new ZipArchive;

                if ($zip->open($filepath) !== true) {
                    throw new Exception('Failed to open Zip file');
                }

                $zip->extractTo(root_dir());
                $zip->close();

                // Restore app configuration files
                $manager->restoreBackup($update);

                // Post Update
                $manager->postUpdate($update);
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

        if (file_exists(storage_dir($zipName))) {
            unlink(storage_dir($zipName));
        }

        copy($zipName, storage_dir($zipName));

        unlink($zipName);
    }

    protected function restoreBackup(): void
    {
        $zip = new ZipArchive;

        if ($zip->open(storage_dir('um_backup.zip')) !== true) {
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

        // reload config/app.php file
        config('app', null, true);

        // remove update from coplay.json
        Configurator::$instance->remove('update');
    }
}
