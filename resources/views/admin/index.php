<?php

use App\Core\Configurator;

$this
    ->layout('admin.layout')
    ->block('title', 'Admin - ' . Configurator::$instance->get('title', 'CoPlay'))
    ->with(['config' => $config])
?>

<main class="container flex flex-row flex-wrap" x-data="{
    tab: $persist('dashboard'),
    link: {id: '', tmdb: '', season: '', episode: '', server: '', link: ''},
    menuOpen: window.matchMedia('(min-width: 768px)').matches,
    newLink() {
        this.tab = 'video', this.link = {id: '', tmdb: '', season: '', episode: '', server: '', link: ''}
    },
    editLink(link) {
        this.link = link, this.tab = 'video'
    }
}">
    <?php $this
        ->include('admin.includes.header')
        ->include('admin.includes.sidebar')
    ?>

    <div class="w-full md:w-9/12 mb-6">
        <?php $this
            ->include('admin.tabs.dashboard')
            ->include('admin.tabs.links', ['links' => $links])
            ->include('admin.tabs.video')
            ->include('admin.tabs.settings')
            ->include('admin.tabs.scripts')
        ?>
    </div>
</main>