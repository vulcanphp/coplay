<?php
$isAjax = request()->accept('application/json');
if (!$isAjax) {
    ?>

    <!DOCTYPE html>
    <html lang="<?= env('lang') ?>">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= _e($title ?? '') ?></title>
        <link rel="icon" type="image/x-icon" href="<?= asset_url('favicon.png') ?>">
        <?= tailwind() ?>
        <!-- Swiper Css customization -->
        <style>
            :root {
                --swiper-theme-color: rgb(var(--color-accent-600)) !important;
                --swiper-navigation-size: 36px !important;
                --swiper-navigation-sides-offset: 14px !important;
                --swiper-pagination-bullet-size: 10px !important;
                --swiper-pagination-bottom: 12px !important;
            }
        </style>
        <style>
            .loader {
                width: 45px;
                aspect-ratio: 1;
                --c1: linear-gradient(90deg, #0000 calc(100%/3), rgb(var(--color-accent-400) / 100) 0 calc(2*100%/3), #0000 0);
                --c2: linear-gradient(0deg, #0000 calc(100%/3), rgb(var(--color-accent-400) / 100) 0 calc(2*100%/3), #0000 0);
                background: var(--c1), var(--c2), var(--c1), var(--c2);
                background-size: 300% 4px, 4px 300%;
                background-repeat: no-repeat;
                animation: l3 1s infinite linear;
            }

            @keyframes l3 {
                0% {
                    background-position: 50% 0, 100% 100%, 0 100%, 0 0
                }

                25% {
                    background-position: 0 0, 100% 50%, 0 100%, 0 0
                }

                50% {
                    background-position: 0 0, 100% 0, 50% 100%, 0 0
                }

                75% {
                    background-position: 0 0, 100% 0, 100% 100%, 0 50%
                }

                75.01% {
                    background-position: 100% 0, 100% 0, 100% 100%, 0 50%
                }

                100% {
                    background-position: 50% 0, 100% 0, 100% 100%, 0 100%
                }
            }

            #preloader {
                position: fixed;
                inset: 0;
                width: 100%;
                height: 100%;
                background: rgb(var(--color-primary-950) / 100);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.body.style.overflow = 'auto';
                const preloader = document.getElementById('preloader');
                preloader.remove();
            });
        </script>
        <?= vite(['running' => false]) ?>
    </head>

    <body class="bg-primary-950 text-primary-50 font-sans" style="overflow:hidden;" x-data="{mobileMenuOpen: false}">

        <div id="preloader">
            <div class="loader"></div>
        </div>

        <?= $template->include('layout/header') ?>
    <?php } ?>

    <div id="app">
        <div><?= $content ?></div>
    </div>

    <?php if (!$isAjax) { ?>

        <?= $template->include('layout/footer') ?>
    </body>

    </html>
<?php } ?>