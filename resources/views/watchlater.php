<?php
$view->layout('layout/master')
    ->set('title', 'Watchlist: ' . cms('title', 'CoPlay') . ' - ' . config('tagline', 'Stream Free Movies & TV Series Online'));
?>

<main class="container my-5" x-data="{
    watchlater: $persist([]), 
    filter: 'all',
    /** time: <?= e(microtime()) ?> */
}">
    <template x-if="watchlater.length > 0">
        <div>
            <div class="text-center md:flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-primary-200 mb-2 md:mb-0"><?= __e('Saved to Watchlist') ?>
                </h2>
                <button @click="watchlater = []" class="text-rose-400 font-semibold"><?= __e('Clear All') ?></button>
            </div>
            <div class="flex justify-center md:justify-start mt-5">
                <button @click="filter = 'all'"
                    :class="filter == 'all' ? 'text-primary-50 border-accent-400' : 'text-primary-400 hover:text-primary-200 border-transparent'"
                    class="border-b-4 text-lg font-semibold"><?= __e('All') ?></button>
                <button @click="filter = 'movie'"
                    :class="filter == 'movie' ? 'text-primary-50 border-accent-400' : 'text-primary-400 hover:text-primary-200 border-transparent'"
                    class="border-b-4 text-lg font-semibold ml-3"><?= __e('Movie') ?></button>
                <button @click="filter = 'tv'"
                    :class="filter == 'tv' ? 'text-primary-50 border-accent-400' : 'text-primary-400 hover:text-primary-200 border-transparent'"
                    class="border-b-4 text-lg font-semibold ml-4"><?= __e('Tv Series') ?></button>
            </div>
        </div>
    </template>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
        <template x-for="(video, index) in watchlater">
            <div x-cloak x-show="filter === 'all' || filter === video.type" class="mt-8">
                <?= $view->include('components/alpine-card') ?>
            </div>
        </template>
    </div>
    <div x-cloak x-show="watchlater.length == 0" class="text-center py-4">
        <h2 class="text-primary-400 text-2xl mb-1"><?= __e('Watchlist is Empty') ?></h2>
        <a class="text-accent-400 hover:text-accent-500" href="<?= url() ?>">&larr; <?= __e('Home') ?></a>
    </div>
</main>