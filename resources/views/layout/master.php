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
        ->setMeta('title', $config->get('title', 'CoPlay') . ' - ' . $config->get('tagline', 'Stream Free Movies & TV Series Online'))
        ->setMeta('description', $config->get('description', 'Stream Free Movies, TV Shows, Anime, and Drama Online with HD Quality. Watch Anywhere Anytime in CoPlay.'))
        ->siteMeta()
    ?>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <?= mixer()
        ->enque('css', resource_url('assets/dist/bundle.min.css'))
        ->deque('css')
    ?>

    <?= mixer()
        ->enque('js', resource_url('assets/dist/bundle.min.js'))
        ->deque('js')
    ?>

    <?= $config->get('head', '') ?>
</head>

<body class="bg-primary-800 font-sans text-white" x-data="{mobileMenuOpen: false}">
    <?= $config->get('body', '') ?>

    <?php $this->include('layout.header') ?>

    {{content}}

    <?php $this->include('layout.footer') ?>

    <?= $config->get('footer', '') ?>
</body>

</html>