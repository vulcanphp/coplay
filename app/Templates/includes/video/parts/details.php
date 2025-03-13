<?php

if (!function_exists('countdown')) {
    function countdown(string $diff): string
    {
        if (time() >= strtotime($diff)) {
            return __e('Aired');
        }

        $dt_end = new DateTime($diff);
        $remain = $dt_end->diff(new DateTime());

        return ($remain->d > 0 ? $remain->d . ' ' . __e('days and') . ' ' : '') . $remain->h . ' ' . __e('hours');
    }
}

?>
<div x-show="tabOpen === 'details'" class="text-primary-300">
    <?php if ($isTv && isset($video->next_episode_to_air['air_date'])): ?>
        <p class="text-primary-400 uppercase mb-1"><?= __e('Upcoming Episode') ?></p>
        <p class="mb-4"><?= $video->next_episode_to_air['name'] ?? 'Episode' ?>
            (<?= countdown($video->next_episode_to_air['air_date']) ?>)</p>
    <?php endif ?>
    <p class="text-primary-400 uppercase mb-1"><?= __e('Release Date') ?></p>
    <p class="text-primary-300 text-sm">
        <?= date('d M, Y', strtotime(strval($isTv ? $video->first_air_date : $video->release_date))) ?>
    </p>
    <?php if (isset($runtime)): ?>
        <p class="text-primary-400 uppercase mt-4 mb-1"><?= __e('Runtime') ?></p>
        <p><?= sprintf('%02d:%02d:', floor($runtime / 60), $runtime - floor($runtime / 60) * 60) ?> <small
                class="text-primary-400">(HH:MM)</small></p>
    <?php endif ?>
    <?php if ($isTv && isset($video->number_of_episodes)): ?>
        <p class="text-primary-400 uppercase mt-4 mb-1"><?= __e('Total Episode/Season') ?></p>
        <p><?= $video->number_of_episodes ?>/<?= $video->number_of_seasons ?? 0 ?></p>
    <?php endif ?>
    <p class="text-primary-400 uppercase mt-4 mb-1"><?= __e('Synopsis') ?></p>
    <h3 class="text-xl font-semibold mb-2 text-primary-200"><?= $video->tagline ?></h3>
    <p><?= $video->overview ?></p>
    <?php if ($title != $original_title): ?>
        <p class="text-primary-400 uppercase mt-4 mb-1"><?= __e('Original Title') ?></p>
        <p><?= $original_title ?></p>
    <?php endif ?>
    <?= $template->include('includes/video/parts/gallery') ?>
</div>