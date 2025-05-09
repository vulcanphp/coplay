<?php use Spark\Support\Str;
$isTv ??= false;
?>
<div class="max-w-sm mx-auto mb-8 md:mb-0 md:w-4/12 lg:w-3/12">

    <h2 class="md:hidden mb-4 text-2xl font-bold text-center">
        <?= e($title) ?>
        <span
            class="text-primary-400 font-semibold">(<?= date('Y', strtotime(strval($isTv ? $video->first_air_date : $video->release_date))) ?>)</span>
    </h2>

    <div class="border border-primary-700 text-center md:text-left">
        <?php if (isset($video->poster_path)): ?>
            <img src="<?= $video->getImageUrl('w342') . $video->poster_path ?>" alt="" class="w-full bg-primary-900">
        <?php endif ?>
        <div class="mb-2 py-3 px-4 rounded-b text-primary-300 bg-primary-800 grid grid-cols-2 gap-2 text-center"
            x-data="manageWatchList(/** time: <?= e(microtime()) ?> */)">
            <div>
                <button x-show="isWatchLater(<?= $video->id ?>)" x-on:click="removeFromWatchLater(<?= $video->id ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="m-auto fill-current text-primary-200" width="30"
                        height="30" viewBox="0 0 24 24">
                        <path
                            d="M12.25 2c-5.514 0-10 4.486-10 10s4.486 10 10 10 10-4.486 10-10-4.486-10-10-10zM18 13h-6.75V6h2v5H18v2z">
                        </path>
                    </svg>
                    <span class="text-xs hidden md:block"><?= __e('Remove') ?></span>
                </button>
                <button x-show="!isWatchLater(<?= $video->id ?>)" x-on:click="addToWatchLater(<?= $video->id ?>)"
                    class="text-primary-400 hover:text-primary-300 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="m-auto fill-current" width="30" height="30"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z">
                        </path>
                        <path d="M13 7h-2v5.414l3.293 3.293 1.414-1.414L13 11.586z"></path>
                    </svg>
                    <span class="text-xs hidden md:block"><?= __e('Watch Later') ?></span>
                </button>
            </div>
            <?= $view->include('includes/video/parts/share') ?>
        </div>
        <?php
        if (strtolower($video->status) !== 'released'): ?>
            <p class="text-center px-4 uppercase text-primary-300 text-sm italic font-semibold">
                (<?= __e($video->status) ?>)
            </p>
        <?php endif ?>
        <div class="py-2 px-4 border-primary-800">
            <p class="mb-1 uppercase text-primary-400 font-semibold"><?= __e('Rating') ?></p>
            <div class="flex items-center gap-1 justify-center md:justify-start">
                <svg class="fill-current text-orange-500 w-5" viewBox="0 0 24 24">
                    <path
                        d="M17.56 21a1 1 0 01-.46-.11L12 18.22l-5.1 2.67a1 1 0 01-1.45-1.06l1-5.63-4.12-4a1 1 0 01-.25-1 1 1 0 01.81-.68l5.7-.83 2.51-5.13a1 1 0 011.8 0l2.54 5.12 5.7.83a1 1 0 01.81.68 1 1 0 01-.25 1l-4.12 4 1 5.63a1 1 0 01-.4 1 1 1 0 01-.62.18z"
                        data-name="star"></path>
                </svg>
                <span class="text-primary-300"><?= $video->vote_average * 10 ?>%</span>
            </div>
        </div>
        <div class="border-t py-2 px-4 border-primary-800">
            <p class="mb-1 uppercase text-primary-400 font-semibold"><?= __e('Genres') ?></p>
            <div class="text-primary-300 text-sm">
                <?= $video->genres(
                    fn($genres) => join(
                        ', ',
                        array_map(
                            fn($id, $title) => sprintf(
                                '<a class="hover:text-accent-500" href="%s">%s</a>',
                                route_url(
                                    'genre',
                                    [
                                        'type' => $isTv ? 'tv' : 'movie',
                                        'slug' => Str::slug($title) . '-' . $id
                                    ]
                                ),
                                $title
                            ),
                            array_column($genres, 'id'),
                            array_column($genres, 'name')
                        )
                    )
                ) ?>
            </div>
        </div>
        <div class="border-t py-2 px-4 border-primary-800">
            <p class="mb-1 uppercase text-primary-400 font-semibold"><?= __e('Languages') ?></p>
            <div class="text-primary-300 text-sm">
                <?= $video->spoken_languages(
                    fn($languages) => join(
                        ', ',
                        array_map(
                            fn($lan, $title) => sprintf(
                                '<a class="hover:text-accent-500" href="%s">%s</a>',
                                route_url(
                                    'language',
                                    [
                                        'type' => $isTv ? 'tv' : 'movie',
                                        'language' => Str::slug($title) . '-' . $lan
                                    ]
                                ),
                                $title
                            ),
                            array_column($languages, 'iso_639_1'),
                            array_column($languages, 'name')
                        )
                    )
                ) ?>
            </div>
        </div>
        <div class="border-t py-2 px-4 border-primary-800">
            <p class="mb-1 uppercase text-primary-400 font-semibold"><?= __e('Countries') ?></p>
            <div class="text-primary-300 text-sm">
                <?= $video->production_countries(
                    fn($countries) => join(
                        ', ',
                        array_map(
                            fn($country, $title) => sprintf(
                                '<a class="hover:text-accent-500" href="%s">%s</a>',
                                route_url(
                                    'country',
                                    [
                                        'type' => $isTv ? 'tv' : 'movie',
                                        'country' => Str::slug($title) . '-' . $country
                                    ]
                                ),
                                $title
                            ),
                            array_column($countries, 'iso_3166_1'),
                            array_column($countries, 'name')
                        )
                    )
                ) ?>
            </div>
        </div>
        <div class="border-t py-2 px-4 border-primary-800">
            <p class="mb-1 uppercase text-primary-400 font-semibold"><?= __e('Companies') ?></p>
            <div class="text-primary-300 text-sm">
                <?= $video->production_companies(
                    fn($companies) => join(
                        ', ',
                        array_map(
                            fn($id, $title) => sprintf(
                                '<a class="hover:text-accent-500" href="%s">%s</a>',
                                route_url(
                                    'company',
                                    [
                                        'type' => $isTv ? 'tv' : 'movie',
                                        'slug' => Str::slug($title) . '-' . $id
                                    ]
                                ),
                                $title
                            ),
                            array_column($companies, 'id'),
                            array_column($companies, 'name')
                        )
                    )
                ) ?>
            </div>
        </div>
        <?php if (isset($video->networks)): ?>
            <div class="border-t py-2 px-4 border-primary-800">
                <p class="mb-1 uppercase text-primary-400 font-semibold"><?= __e('Networks') ?></p>
                <div class="text-primary-300 text-sm">
                    <?= $video->networks(
                        fn($networks) => join(
                            ', ',
                            array_map(
                                fn($id, $title) => sprintf(
                                    '<a class="hover:text-accent-500" href="%s">%s</a>',
                                    route_url(
                                        'network',
                                        [
                                            'type' => $isTv ? 'tv' : 'movie',
                                            'slug' => Str::slug($title) . '-' . $id
                                        ]
                                    ),
                                    $title
                                ),
                                array_column($networks, 'id'),
                                array_column($networks, 'name')
                            )
                        )
                    ) ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>