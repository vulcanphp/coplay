<?php
$template->layout('layout/master')
    ->set('title', '404: ' . cms('title', 'CoPlay') . ' - Page Not Found')
    ?>

<main class="mx-auto w-8/12 sm:w-6/12 md:w-5/12 lg:w-4/12 xl:w-3/12 text-center my-16">
    <h2 class="text-accent-400 text-6xl font-semibold mb-4">404</h2>
    <p class="text-xl text-primary-200 mb-4"><?= __e('Sorry, the page you are looking for does not exists.') ?></p>
    <a href="<?= url() ?>" class="text-accent-400 hover:text-accent-500">&larr; <?= __e('Home') ?></a>
</main>