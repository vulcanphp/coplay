<?php

use App\Core\Configurator;

$this
    ->layout('layout.master')
    ->setupMeta([
        'title' => 'Explore: ' . Configurator::$instance->get('title', 'CoPlay') . ' - Stream Free Movies & TV Series Online'
    ])
?>

<main class="container my-5">

    <h2 class="text-center text-3xl font-semibold text-gray-200 mb-1"><?= $heading ?></h2>

    <?php if ($paginator->hasLinks()) : ?>
        <div class="flex justify-center"><?= $paginator->getLinks() ?></div>
    <?php endif ?>

    <?php if ($paginator->hasData()) : ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            <?php foreach ($paginator->getData() as $video) {
                $this->component('components.video-card', ['video' => $video]);
            } ?>
        </div>
    <?php endif ?>

    <?php if ($paginator->hasLinks()) : ?>
        <div class="flex justify-center mt-6"><?= $paginator->getLinks() ?></div>
    <?php endif ?>

</main>