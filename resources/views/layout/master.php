<?php

use App\Core\Configurator;

if (!isset($config)) {
    $this->with(['config' => $config = Configurator::$instance]);
}
?>
<!DOCTYPE html>
<html lang="<?= __lang() ?>">

<head>
    <?= $this
        ->setMeta('language', __lang())
        ->setMeta('url', url()->absoluteUrl())
        ->setMeta('sitename', $config->get('title', 'CoPlay'))
        ->setMeta('description', $config->get('description', 'Stream Free Movies, TV Shows, Anime, and Drama Online with HD Quality. Watch Anywhere Anytime in CoPlay.'))
        ->siteMeta()
    ?>
    <link rel="icon" type="image/x-icon" href="<?= resource_url('assets/favicon.png') ?>">
    <?= fire_link() ?>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <?= mixer()
        ->enque('css', resource_url('assets/dist/bundle.min.css'))
        ->deque('css')
    ?>
    <script defer src="<?= resource_url('assets/dist/bundle.min.js') ?>"></script>
    <?= $config->get('head', '') ?>
</head>

<body class="bg-primary-800 font-sans text-white" x-data="{mobileMenuOpen: false, isFireError: false}">

    <!-- Fire Loading Progressbar -->
    <div x-data="{width: 0, interval: null}" class="fixed top-0 inset-x-0 z-50">
        <div x-cloak x-show="width > 0" class="bg-slate-800 h-[3px]" role="progressbar" :aria-valuenow="width" aria-valuemin="0" aria-valuemax="100">
            <div class="bg-amber-400 rounded-lg h-full" :style="`width: ${width}%; transition: width 0.5s;`"></div>
        </div>
        <div x-init="window.fireView.on('beforeLoad', () => (isFireError = false, width = 10, interval = setInterval(() => width = (width >= 80 ? 80 : width + 10), 25)))"></div>
        <div x-init="window.fireView.on('afterLoad', () => (clearInterval(interval), width = 100, window.scrollTo({top: 0, behavior: 'smooth'}), setTimeout(() => width = 0, 450)))"></div>
        <div x-init="window.fireView.on('onError', () => isFireError = true)"></div>
    </div>
    <!-- Fire Loading Progressbar -->

    <?= $config->get('body', '') ?>

    <?php $this->include('layout.header') ?>

    <div x-cloak x-show="!isFireError" fire="content">
        {{content}}
    </div>

    <div x-cloak x-show="isFireError" x-transition class="mx-auto w-8/12 sm:w-6/12 md:w-5/12 lg:w-4/12 xl:w-3/12 text-center my-10">
        <h2 class="text-amber-400 text-6xl font-semibold mb-4">Oops!</h2>
        <p class="text-xl text-gray-300 mb-4"><?= translate('Something Wen\'t Wrong, Please Try Again Later.') ?></p>
        <a @click.prevent="isFireError = false" href="#" class="text-amber-400"><?= translate('&larr; Go Back') ?></a>
    </div>

    <?php $this->include('layout.footer') ?>

    <?= $config->get('footer', '') ?>

    <?= mixer()->deque('js') ?>
</body>

</html>