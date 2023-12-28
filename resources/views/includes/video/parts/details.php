<?php

if (!function_exists('countdown')) {
    function countdown(string $diff): string
    {
        if (time() >= strtotime($diff)) {
            return translate('Aired');
        }

        $dt_end = new DateTime($diff);
        $remain = $dt_end->diff(new DateTime());

        return ($remain->d > 0 ? $remain->d . ' ' . translate('days and') . ' ' : '') . $remain->h . ' ' . translate('hours');
    }
}

use VulcanPhp\Core\Helpers\Time;
?>
<div x-show="tabOpen == 'details'" class="text-gray-300">
    <?php if ($isTv && isset($video->next_episode_to_air['air_date'])) : ?>
        <p class="text-gray-400 uppercase mb-1"><?= translate('Upcoming Episode') ?></p>
        <p class="mb-4"><?= $video->next_episode_to_air['name'] ?? 'Episode' ?> (<?= countdown($video->next_episode_to_air['air_date']) ?>)</p>
    <?php endif ?>
    <p class="text-gray-400 uppercase mb-1"><?= translate('Release Date') ?></p>
    <p class="text-gray-300 text-sm"><?= Time::dateFormat($isTv ? $video->first_air_date : $video->release_date) ?></p>
    <?php if (isset($runtime)) : ?>
        <p class="text-gray-400 uppercase mt-4 mb-1"><?= translate('Runtime') ?></p>
        <p><?= sprintf('%02d:%02d:', floor($runtime / 60), ($runtime -   floor($runtime / 60) * 60)) ?> <small class="text-gray-400">(HH:MM)</small></p>
    <?php endif ?>
    <?php if ($isTv && isset($video->number_of_episodes)) : ?>
        <p class="text-gray-400 uppercase mt-4 mb-1"><?= translate('Total Episode/Season') ?></p>
        <p><?= $video->number_of_episodes ?>/<?= $video->number_of_seasons ?? 0 ?></p>
    <?php endif ?>
    <p class="text-gray-400 uppercase mt-4 mb-1"><?= translate('Synopsis') ?></p>
    <h3 class="text-xl font-semibold mb-2 text-gray-200"><?= $video->tagline ?></h3>
    <p><?= $video->overview ?></p>
    <?php if ($title != $original_title) : ?>
        <p class="text-gray-400 uppercase mt-4 mb-1"><?= translate('Original Title') ?></p>
        <p><?= $original_title ?></p>
    <?php endif ?>
    <?php $this->include('includes.video.parts.gallery') ?>
</div>