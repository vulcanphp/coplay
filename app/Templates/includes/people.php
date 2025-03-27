<?php if (isset($people)): ?>
    <section class="mt-8" x-data="swiper({
        /** time: <?= _e(microtime()) ?> */
        slidesPerView: 2,
        spaceBetween: 16,
        breakpoints: {
            768: {
                slidesPerView: 3,
            },
            1024: {
                slidesPerView: 4,
            },
            1280: {
                slidesPerView: 5,
            },
        },
    })">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-2xl border-accent-400 border-b-4 inline-block leading-10 mb-2">
                <?= __('Popular People') ?>
            </h2>
            <!-- Navigation -->
            <div class="flex items-center gap-1.5">
                <button x-on:click="slider().slidePrev()"
                    class="bg-primary-900/65 p-1 rounded-sm hover:bg-primary-900 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd"
                            d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <button x-on:click="slider().slideNext()"
                    class="bg-primary-900/65 p-1 rounded-sm hover:bg-primary-900 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd"
                            d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php foreach ($people as $person): ?>
                    <div class="swiper-slide">
                        <?= $template->include('components/person', ['person' => $person]) ?>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>
<?php endif ?>