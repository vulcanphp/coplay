<?php
$template->layout('layout/master')
    ->set('title', 'Explore: ' . cms('title', 'CoPlay') . ' - ' . env('tagline', 'Stream Free Movies & TV Series Online'));
?>

<main class="container my-5">

    <h2 class="text-center text-3xl font-semibold text-primary-200 mb-2"><?= $heading ?></h2>

    <?php if ($paginator->hasLinks()): ?>
        <?php $links = $paginator->getLinks(entity: [
            'next' => __e('Next'),
            'prev' => __e('Prev'),
        ]) ?>
        <div class="flex justify-center"><?= $links ?></div>
    <?php endif ?>

    <?php if ($paginator->hasData()): ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            <?php foreach ($paginator->getData() as $video) {
                echo $template->include('components/video-card', ['video' => $video]);
            } ?>
        </div>
    <?php endif ?>

    <?php if ($paginator->hasLinks()): ?>
        <div class="flex justify-center mt-6"><?= $links ?></div>
    <?php endif ?>

</main>