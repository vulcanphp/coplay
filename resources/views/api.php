<?php
$view->layout('layout/master')
    ->set('title', 'Fast & Free Video Streaming API - ' . cms('title', 'CoPlay'));
?>

<main class="container my-16 md:px-28 lg:px-32 xl:px-36" x-data="{
    tabOpen: 'movie',
    frame: '',
    tmdb: 572802,
    season: 1,
    episode: 1,
    initFrame() {
        const apiEndpoint = '<?= e(route_url('embed')) ?>';
        this.frame = apiEndpoint.replace('{type}', this.tabOpen).replace('{id}', this.tmdb) + (this.tabOpen == 'tv' ? '?season=' + this.season + '&episode=' + this.episode : '')
    },
    switchTab(type) {
        type != this.tabOpen && (this.tabOpen = type, this.tmdb = (type == 'tv' ? 108978 : 572802), this.season = 1, this.episode = 1, this.initFrame()) 
    },
    /** time: <?= e(microtime()) ?> */
}">
    <?= $view->include('includes/api/intro'); ?>
    <?= $view->include('includes/api/player'); ?>
    <?= $view->include('includes/api/feature'); ?>
</main>