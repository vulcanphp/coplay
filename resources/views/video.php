<?php

use App\Core\Configurator;

$this->layout('layout.master');

$isTv           = isset($video->name) && !isset($video->title);
$title          = $isTv ? $video->name : $video->title;
$original_title = $isTv ? $video->original_name : $video->original_title;
$runtime        = $isTv && !empty($video->episode_run_time) ? array_sum($video->episode_run_time) / count($video->episode_run_time) : ($video->runtime ?? null);

if ($isTv) {
    $seasons = collect($video->seasons)
        ->filter(fn ($se) => ($se['season_number'] ?? 0) > 0 && isset($se['air_date']) && strtotime($se['air_date']) <= time())
        ->all();

    if (!empty($seasons)) {
        $video->number_of_seasons = max(array_column($seasons, 'season_number'));
        unset($seasons);
    }
}

$this
    ->with([
        'isTv'              => $isTv,
        'video'             => $video,
        'similar'           => $similar,
        'title'             => $title,
        'original_title'    => $original_title,
        'runtime'           => $runtime
    ])
    ->setupMeta([
        'title' => 'Watch ' . $title . ' Free Online in ' . Configurator::$instance->get('title', 'CoPlay'),
        'description' => $video->overview,
        'image' => $video->getImageUrl('w342') . $video->poster_path
    ]);

?>

<main x-data='{
        isPlaying: false,
        FrameUrl: "",
        closePlayer: false,
        isLoading: false,
        <?= $isTv ?
            'season: $persist(' . $video->number_of_seasons . ').using(sessionStorage).as("se_' . $video->id . '"),'
            : ''
        ?>
        playMovie() {
            this.isPlaying = true,
            this.isLoading = true,
            this.FrameUrl = "<?= url('embed', ['type' => ($isTv ? 'tv' : 'movie'), 'id' => $video->id]) ?>" + "?related=1&remember=1" <?= $isTv ? '+ "&season=" + this.season' : '' ?>
        },
        watchlater: $persist([]),
        isWatchLater: false,
        removeFromWatchLater() {
            (this.watchlater = this.watchlater.filter((vid) => vid.id != <?= $video->id ?>)) && this.checkWatchLater()
        },
        addToWatchLater() {
            this.watchlater.unshift({
                title: "<?= preg_replace('/[^a-zA-Z0-9 ]+/', '', $video->title ?? $video->name) ?>",
                id: <?= $video->id ?>,
                type: "<?= isset($video->name) ? 'tv' : 'movie' ?>",
                poster: "<?= $video->poster_path ?? null ?>"
            }) && this.checkWatchLater()
        },
        checkWatchLater() {
            this.isWatchLater = (this.watchlater.filter((vid) => vid.id == <?= $video->id ?>).length == 1)
        }
    }'>

    <?php $this->include('includes.video.player') ?>

    <div :class="!isPlaying && 'md:mt-[-50px]'" class="container relative z-30 rounded-xl bg-primary-800">
        <div class="flex py-5" :class="!isPlaying && 'md:px-1'">
            <?php $this
                ->include('includes.video.sidebar')
                ->include('includes.video.info')
            ?>
        </div>
    </div>

</main>