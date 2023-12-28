<?php

namespace App\Http\Kernels;

use App\Core\Configurator;
use VulcanPhp\Core\Foundation\Interfaces\IKernel;
use VulcanPhp\Translator\Manager\TranslatorFileManager;
use VulcanPhp\Translator\Translator;

class AppKernel implements IKernel
{
    public function boot(): void
    {
        // CoPlay App Configurator
        $config = Configurator::configure(
            storage_dir('cooplay.json')
        );

        // configure CoPlay
        if (!$config->is('configured') && url()->getPath() != '/admin/') {
            redirect('admin');
        }

        // set language manager
        Translator::$instance->getDriver()
            ->setManager(new TranslatorFileManager([
                'convert'   => $config->get('language', config('app.language')),
                'local_dir' => config('app.language_dir'),
            ]));
    }

    public function shutdown(): void
    {
    }
}
