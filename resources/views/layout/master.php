<?php
$isFireline = request()->isFirelineRequest();
if (!$isFireline) {
    ?>

    <!DOCTYPE html>
    <html lang="<?= config('lang') ?>">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($title ?? '') ?></title>
        <link rel="icon" type="image/x-icon" href="<?= asset_url('favicon.png') ?>">
        <?= tailwind() ?>
        <!-- Swiper Css customization -->
        <style>
            :root {
                --swiper-theme-color: var(--color-accent-600) !important;
                --swiper-navigation-size: 36px !important;
                --swiper-navigation-sides-offset: 14px !important;
                --swiper-pagination-bullet-size: 10px !important;
                --swiper-pagination-bottom: 12px !important;
            }
        </style>
    </head>

    <body class="bg-primary-950 text-primary-50 font-sans" x-data="{mobileMenuOpen: false}">

        <?= tailwind()->getPreloaderElement() ?>

        <?= $view->include('layout/header') ?>
        <div id="app">
        <?php } ?>

        <div><?= $content ?></div>

        <?php if (!$isFireline) { ?>
        </div>

        <?= $view->include('layout/footer') ?>
    </body>

    </html>
<?php } ?>