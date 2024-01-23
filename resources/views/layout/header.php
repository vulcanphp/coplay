<?php

$menus = [
    home_url('movie') => 'Movie',
    home_url('tv') => 'Tv',
    home_url('watchlist') => 'Watchlist',
];

if ($config->is('api')) {
    $menus[home_url('api')] = 'Api';
}

?>
<header class="md:fixed z-40 bg-primary-800 inset-x-0 top-0 py-4 md:py-0">
    <div class="container">
        <div class="md:h-16 flex flex-col md:flex-row items-center justify-between">
            <a href="<?= home_url() ?>" class="flex items-center text-amber-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zm.001 6c-.001 0-.001 0 0 0h-.465l-2.667-4H20l.001 4zM9.536 9 6.869 5h2.596l2.667 4H9.536zm5 0-2.667-4h2.596l2.667 4h-2.596zM4 5h.465l2.667 4H4V5zm0 14v-8h16l.002 8H4z"></path>
                    <path fill="currentColor" d="m10 18 5.5-3-5.5-3z"></path>
                </svg>
                <span class="font-semibold text-xl ml-3"><?= $config->get('title', 'CoPlay') ?></span>
            </a>
            <div class="hidden md:flex items-center">
                <?php foreach ($menus as $url => $menu) : ?>
                    <a href="<?= $url ?>" class="px-3 py-2 hover:text-gray-300"><?= translate($menu) ?></a>
                <?php endforeach ?>
            </div>
            <div class="relative mt-3 md:mt-0" x-data="{search: '', isOpen: true, isLoading: false, fetchResult(){
                    this.isLoading = true
                    fetch('/search?keyword=' + this.search).then(res => res.text()).then(html => {
                        document.querySelector('#searchResult').innerHTML = html, this.isLoading = false
                    });
                }}" @click.away="isOpen = false">
                <input type="text" placeholder="<?= translate("Search (Press '/' to focus)") ?>" x-ref="search" @keydown.window="
                    if(event.keyCode === 191){
                        event.preventDefault();
                        $refs.search.focus();
                    }" @input.debounce.500ms="fetchResult" x-model="search" @focus="isOpen = true" @keydown="isOpen = true" @keydown.escape.window="isOpen = false" @keydown.shift.tab="isOpen = false" class="bg-gray-800 text-sm rounded-full w-64 px-4 pl-9 py-[6px] focus:outline-none focus:ring-2 ring-amber-500/75" />
                <div class="absolute top-0">
                    <svg class="fill-current w-4 text-gray-500 mt-2 ml-3" viewBox="0 0 24 24">
                        <path d="M16.32 14.9l5.39 5.4a1 1 0 01-1.42 1.4l-5.38-5.38a8 8 0 111.41-1.41zM10 16a6 6 0 100-12 6 6 0 000 12z"></path>
                    </svg>
                </div>
                <div x-cloak x-show="isLoading" x-transition class="absolute top-0 right-0">
                    <svg class="animate-spin fill-current w-4 text-gray-500 mt-2 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div x-cloak x-show="isOpen" x-transition id="searchResult" class="absolute z-40 top-full w-full h-max"></div>
            </div>
            <button @click="mobileMenuOpen = true" class="md:hidden mt-3 p-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M4 6h16v2H4zm4 5h12v2H8zm5 5h7v2h-7z"></path>
                </svg>
            </button>
        </div>
    </div>
</header>

<div class="md:h-16"></div>

<aside x-cloak x-show="mobileMenuOpen" x-transition class="md:hidden container p-0 fixed inset-0 z-40 h-full bg-gray-900 text-center">
    <button class="mt-3 p-2 hover:text-gray-300" @click="mobileMenuOpen = false">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
            <path fill="currentColor" d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"></path>
        </svg>
    </button>
    <?php foreach ($menus as $url => $menu) : ?>
        <a href="<?= $url ?>" class="hover:text-gray-300 text-lg px-4 py-2 mt-1 block"><?= translate($menu) ?></a>
    <?php endforeach ?>
</aside>