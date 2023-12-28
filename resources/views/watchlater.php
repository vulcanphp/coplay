<?php

use App\Core\Configurator;

$this
    ->layout('layout.master')
    ->setupMeta([
        'title' => 'Watchlist: ' . Configurator::$instance->get('title', 'CoPlay') . ' - Stream Free Movies & TV Series Online'
    ])
?>

<main class="container my-5" x-data="{watchlater: $persist([]), filter: 'all'}">
    <template x-if="watchlater.length > 0">
        <div>
            <div class="text-center md:flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-gray-200 mb-2 md:mb-0"><?= translate('Saved to Watchlist') ?></h2>
                <button @click="watchlater = []" class="text-rose-400 font-semibold"><?= translate('Clear All') ?></button>
            </div>
            <div class="flex justify-center md:justify-start mt-5">
                <button @click="filter = 'all'" :class="filter == 'all' ? 'text-white border-amber-400' : 'text-gray-400 hover:text-gray-200 border-transparent'" class="border-b-4 text-lg font-semibold"><?= translate('All') ?></button>
                <button @click="filter = 'movie'" :class="filter == 'movie' ? 'text-white border-amber-400' : 'text-gray-400 hover:text-gray-200 border-transparent'" class="border-b-4 text-lg font-semibold ml-3"><?= translate('Movie') ?></button>
                <button @click="filter = 'tv'" :class="filter == 'tv' ? 'text-white border-amber-400' : 'text-gray-400 hover:text-gray-200 border-transparent'" class="border-b-4 text-lg font-semibold ml-4"><?= translate('Tv Series') ?></button>
            </div>
        </div>
    </template>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
        <template x-for="video in watchlater">
            <?php $this->include('components.alpine-card') ?>
        </template>
    </div>
    <template x-if="watchlater.length == 0">
        <div class="text-center py-4">
            <h2 class="text-gray-400 text-2xl mb-1"><?= translate('Watchlist is Empty') ?></h2>
            <a class="text-amber-400" href="<?= home_url() ?>"><?= translate('&larr; Home') ?></a>
        </div>
    </template>
</main>