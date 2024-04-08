<?php

use App\Core\Configurator;

$this
    ->layout('layout.master')
    ->block('title', '404: ' . Configurator::$instance->get('title', 'CoPlay') . ' - Page Not Found')
?>

<main class="mx-auto w-8/12 sm:w-6/12 md:w-5/12 lg:w-4/12 xl:w-3/12 text-center my-10">
    <h2 class="text-amber-400 text-6xl font-semibold mb-4">404</h2>
    <p class="text-xl text-gray-300 mb-4"><?= translate('Sorry, the page you are looking for does not exists.') ?></p>
    <a fire href="<?= home_url() ?>" class="text-amber-400"><?= translate('&larr; Home') ?></a>
</main>