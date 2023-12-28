<?php

use App\Core\Configurator;

$this
    ->layout('layout.master')
    ->setupMeta([
        'title' => 'Fast & Free Video Streaming API - ' . Configurator::$instance->get('title', 'CoPlay'),
        'description' => 'We provide up-to-date working free streaming links for movies and tv series that can be integrated into your website through our embed links API'
    ])
?>

<main class="container my-16 md:px-28 lg:px-32 xl:px-36" x-data="{
    tabOpen: 'movie',
    frame: '',
    tmdb: 572802,
    season: 1,
    episode: 1,
    initFrame() {
        this.frame = '<?= home_url('embed') ?>/' + this.tabOpen + '/' + this.tmdb + (this.tabOpen == 'tv' ? '?season=' + this.season + '&episode=' + this.episode : '')
    },
    switchTab(type) {
        type != this.tabOpen && (this.tabOpen = type, this.tmdb = (type == 'tv' ? 108978 : 572802), this.season = 1, this.episode = 1, this.initFrame()) 
    }
}">
    <?php $this
        ->include('includes.api.intro')
        ->include('includes.api.player')
        ->include('includes.api.feature')
    ?>
</main>