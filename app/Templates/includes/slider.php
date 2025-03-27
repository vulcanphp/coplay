<?php if (isset($sliderItems) && !empty($sliderItems)): ?>
    <section x-data="swiper({
        /** time: <?= _e(microtime()) ?> */
        loop: true,
        slidesPerView: 1,
        spaceBetween: 0,
        parallax: true,
        effect: 'fade',
        autoplay: {
            delay: 3000,
            pauseOnMouseEnter: true,
        },
    })" class="-mx-4 sm:mx-auto">
        <div class="swiper h-[40vh] md:h-[50vh] group mb-8 rounded-xs overflow-hidden relative">
            <div class="swiper-wrapper">
                <?php foreach ($sliderItems as $sliderItem):
                    $isTv = isset($sliderItem['name']) && isset($sliderItem['first_air_date']);
                    ?>
                    <!-- Slider Item Start -->
                    <div x-cloak class="swiper-slide">
                        <!-- Slider Content START -->
                        <div
                            class="relative z-10 flex justify-center text-center md:justify-start md:text-left items-end h-full py-8 px-6 bg-gradient-to-b from-primary-950/10 to-primary-950">
                            <div class="w-10/12 md:w-8/12">
                                <div data-swiper-parallax="-200" data-swiper-parallax-duration="325">
                                    <h2 class="text-xl md:text-2xl lg:text-3xl font-semibold mb-4">
                                        <?= _e($sliderItem['name'] ?? $sliderItem['title']) ?>
                                    </h2>
                                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                                        <div class="flex items-center gap-0.5 text-sm">
                                            <svg class="fill-current text-orange-500 size-4" viewBox="0 0 24 24">
                                                <path
                                                    d="M17.56 21a1 1 0 01-.46-.11L12 18.22l-5.1 2.67a1 1 0 01-1.45-1.06l1-5.63-4.12-4a1 1 0 01-.25-1 1 1 0 01.81-.68l5.7-.83 2.51-5.13a1 1 0 011.8 0l2.54 5.12 5.7.83a1 1 0 01.81.68 1 1 0 01-.25 1l-4.12 4 1 5.63a1 1 0 01-.4 1 1 1 0 01-.62.18z"
                                                    data-name="star"></path>
                                            </svg>
                                            <span><?= ceil($sliderItem['vote_average'] * 10) ?>%</span>
                                        </div>
                                        <div class="flex items-center gap-0.5 text-sm">
                                            <?php if ($isTv): ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                                    class="size-4">
                                                    <path fill-rule="evenodd"
                                                        d="M2 10a8 8 0 1 1 16 0 8 8 0 0 1-16 0Zm6.39-2.908a.75.75 0 0 1 .766.027l3.5 2.25a.75.75 0 0 1 0 1.262l-3.5 2.25A.75.75 0 0 1 8 12.25v-4.5a.75.75 0 0 1 .39-.658Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span><?= __e('TV') ?></span>
                                            <?php else: ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-1.5A1.125 1.125 0 0 1 18 18.375M20.625 4.5H3.375m17.25 0c.621 0 1.125.504 1.125 1.125M20.625 4.5h-1.5C18.504 4.5 18 5.004 18 5.625m3.75 0v1.5c0 .621-.504 1.125-1.125 1.125M3.375 4.5c-.621 0-1.125.504-1.125 1.125M3.375 4.5h1.5C5.496 4.5 6 5.004 6 5.625m-3.75 0v1.5c0 .621.504 1.125 1.125 1.125m0 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m1.5-3.75C5.496 8.25 6 7.746 6 7.125v-1.5M4.875 8.25C5.496 8.25 6 8.754 6 9.375v1.5m0-5.25v5.25m0-5.25C6 5.004 6.504 4.5 7.125 4.5h9.75c.621 0 1.125.504 1.125 1.125m1.125 2.625h1.5m-1.5 0A1.125 1.125 0 0 1 18 7.125v-1.5m1.125 2.625c-.621 0-1.125.504-1.125 1.125v1.5m2.625-2.625c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125M18 5.625v5.25M7.125 12h9.75m-9.75 0A1.125 1.125 0 0 1 6 10.875M7.125 12C6.504 12 6 12.504 6 13.125m0-2.25C6 11.496 5.496 12 4.875 12M18 10.875c0 .621-.504 1.125-1.125 1.125M18 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m-12 5.25v-5.25m0 5.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125m-12 0v-1.5c0-.621-.504-1.125-1.125-1.125M18 18.375v-5.25m0 5.25v-1.5c0-.621.504-1.125 1.125-1.125M18 13.125v1.5c0 .621.504 1.125 1.125 1.125M18 13.125c0-.621.504-1.125 1.125-1.125M6 13.125v1.5c0 .621-.504 1.125-1.125 1.125M6 13.125C6 12.504 5.496 12 4.875 12m-1.5 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M19.125 12h1.5m0 0c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h1.5m14.25 0h1.5" />
                                                </svg>
                                                <span><?= __e('Movie') ?></span>
                                            <?php endif ?>
                                        </div>
                                        <div class="flex items-center gap-0.5 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                                class="size-4">
                                                <path fill-rule="evenodd"
                                                    d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span><?= date('d M, Y', strtotime($sliderItem['first_air_date'] ?? $sliderItem['release_date'])) ?></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap justify-center md:justify-start gap-x-3 gap-y-1.5 mt-2">
                                        <?php foreach ($sliderItem['genres'] ?? [] as $id => $genre): ?>
                                            <a href="<?= route_url(
                                                'genre',
                                                [
                                                    'type' => $isTv ? 'tv' : 'movie',
                                                    'slug' => slugify($genre) . '-' . $id
                                                ]
                                            ) ?>"
                                                class="text-sm text-primary-100/85 hover:text-accent-400 transition"><?= _e($genre) ?></a>
                                        <?php endforeach ?>
                                    </div>
                                    <div class="mb-5 mt-2">
                                        <p class="hidden md:block text-[0.8rem]">
                                            <?= trim_words($sliderItem['overview'] ?? '', 30) ?>
                                        </p>
                                    </div>
                                    <a href="<?= url(($isTv ? 'tv' : 'movie') . '/' . slugify($sliderItem['name'] ?? $sliderItem['title']) . '-' . $sliderItem['id']) ?>"
                                        class="inline-flex items-center gap-1.5 text-sm px-4 py-2 rounded-full bg-accent-100 text-accent-800 font-medium hover:bg-accent-50 hover:text-accent-900 hover:scale-105 duration-75 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="size-5">
                                            <path
                                                d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.841Z" />
                                        </svg>
                                        <span><?= __e('Watch Now') ?></span>
                                    </a>
                                </div>
                            </div> <!-- Slider Content END -->
                        </div>

                        <!-- Slider Item Image -->
                        <img src="https://image.tmdb.org/t/p/w1280/<?= $sliderItem['backdrop_path'] ?>"
                            data-swiper-parallax="-300" data-swiper-parallax-duration="300"
                            class="absolute w-full h-full inset-0 object-cover z-0" loading="lazy">

                        <!-- Preloader -->
                        <div class="swiper-lazy-preloader"></div>
                    </div> <!-- Slider Item End -->
                <?php endforeach ?>
            </div>
            <!-- Navigation -->
            <div class="hidden md:flex flex-col absolute right-2 bottom-4 z-30 gap-1.5">
                <button x-on:click="slider().slideNext()"
                    class="bg-primary-800/65 p-1 rounded-sm hover:bg-primary-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd"
                            d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <button x-on:click="slider().slidePrev()"
                    class="bg-primary-800/65 p-1 rounded-sm hover:bg-primary-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd"
                            d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </section>
<?php endif ?>