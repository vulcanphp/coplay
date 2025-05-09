<?php

$isTv = isset($video->name) && !isset($video->title);
$title = $isTv ? $video->name : $video->title;

$view->layout('layout/master')
    ->set('title', 'Watch ' . $title . ' Free Online in ' . cms('title', 'CoPlay'));

$original_title = $isTv ? $video->original_name : $video->original_title;
$runtime = $isTv && !empty($video->episode_run_time) ? array_sum($video->episode_run_time) / count($video->episode_run_time) : ($video->runtime ?? null);

if ($isTv) {
    $seasons = collect($video->seasons)
        ->filter(fn($se) => ($se['season_number'] ?? 0) > 0 && isset($se['air_date']) && strtotime($se['air_date']) <= time())
        ->all();

    if (!empty($seasons)) {
        $video->number_of_seasons = max(array_column($seasons, 'season_number'));
        unset($seasons);
    }
}

$view->set('isTv', $isTv)
    ->set('video', $video)
    ->set('similar', $similar)
    ->set('title', $title)
    ->set('original_title', $original_title)
    ->set('runtime', $runtime);

?>

<main x-data="videoInfo()">

    <!-- Reset The Video Player -->
    <div x-init="() => {
        /** time: <?= e(microtime()) ?> */
        cancelPlay();
    }"></div>

    <?= $view->include('includes/video/player') ?>

    <div :class="!isPlaying && 'md:mt-[-50px]'" class="container relative z-30 rounded-xl bg-primary-900">
        <div class="flex flex-col md:flex-row py-5" :class="!isPlaying && 'md:px-1'">
            <?= $view->include('includes/video/sidebar'); ?>
            <?= $view->include('includes/video/info'); ?>
        </div>
    </div>

</main>
<script>
    function videoInfo() {
        return {
            isPlaying: false,
            FrameUrl: '',
            closePlayer: false,
            isLoading: false,
            playMovie(frameUrl) {
                this.isPlaying = true;
                this.isLoading = true;
                this.FrameUrl = frameUrl;
            },
            cancelPlay() {
                this.isPlaying = false;
                this.FrameUrl = '';
                this.closePlayer = false;
                this.isLoading = false;
            }
        };
    }

    function manageWatchList() {
        return {
            watchlater: Alpine.$persist([]),
            removeFromWatchLater(vId) {
                this.watchlater = this.watchlater.filter((vid) => vid.id !== vId);
            },
            addToWatchLater() {
                this.watchlater.unshift({
                    title: "<?= e($video->title ?? $video->name) ?>",
                    id: <?= $video->id ?>,
                    type: "<?= isset($video->name) ? 'tv' : 'movie' ?>",
                    poster: "<?= $video->poster_path ?? null ?>"
                });
            },
            isWatchLater(vId) {
                return (this.watchlater.filter((vid) => vid.id === vId).length === 1);
            }
        };
    }
</script>