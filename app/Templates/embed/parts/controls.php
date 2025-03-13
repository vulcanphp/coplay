<!-- @Loader: Iframe Loading Status -->
<div x-cloak x-show="isLoading" x-transition
    class="bg-primary-900 absolute inset-0 flex items-center justify-center w-full h-full z-20">
    <svg class="animate-spin -ml-1 mr-3 text-primary-50" xmlns="http://www.w3.org/2000/svg" fill="none" width="40"
        height="40" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
        </path>
    </svg>
</div>

<!-- @Top Buttons for Server and Episodes -->
<div
    class="hidden group-hover:flex items-center absolute z-40 bg-accent-400 font-semibold text-lg w-max top-0 inset-x-0 mx-auto rounded-b-xl">
    <button @click="serverOpen = true" :class="!isTv && 'rounded-br-xl'"
        class="flex items-center rounded-bl-xl hover:bg-accent-500 px-3 py-1 md:text-xl md:px-5 md:py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-[20px] h-[20px] md:w-[28px] md:h-[28px]" viewBox="0 0 24 24">
            <path fill="currentColor"
                d="M20 3H4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-5 5h-2V6h2zm4 0h-2V6h2zm1 5H4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2zm-5 5h-2v-2h2zm4 0h-2v-2h2z">
            </path>
        </svg>
        <span class="ml-1 md:ml-2"><?= __e('Switch Server') ?></span>
    </button>
    <template x-if="isTv">
        <button @click="episodeOpen = true"
            class="flex items-center rounded-br-xl hover:bg-accent-500 px-3 py-1 md:text-xl md:px-5 md:py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-[20px] h-[20px] md:w-[28px] md:h-[28px]"
                viewBox="0 0 24 24">
                <path fill="currentColor"
                    d="M13 16.493C13 18.427 14.573 20 16.507 20s3.507-1.573 3.507-3.507c0-.177-.027-.347-.053-.517H20V6h2V4h-3a1 1 0 0 0-1 1v8.333a3.465 3.465 0 0 0-1.493-.346A3.51 3.51 0 0 0 13 16.493zM2 5h14v2H2z">
                </path>
                <path fill="currentColor" d="M2 9h14v2H2zm0 4h9v2H2zm0 4h9v2H2z"></path>
            </svg>
            <span class="ml-1 md:ml-2"><?= __e('Episode List') ?></span>
        </button>
    </template>
</div>

<!-- @headline: video title -->
<div class="absolute select-none top-12 md:top-14 inset-x-0 w-10/12 text-center mx-auto hidden group-hover:block z-40">
    <h2 class="text-sm sm:text-base md:text-lg whitespace-nowrap overflow-hidden text-ellipsis text-accent-400 font-semibold"
        x-text="title + ' ('+ year +')' + (isTv ? ' S' + season + ' E' + episode : '')"></h2>
</div>

<!-- @Left & Right Buttons for Next and prev episode -->
<template x-if="isTv">
    <div>
        <div x-show="prevEp != null" @click="switchEpisode(prevEp)"
            class="hidden group-hover:block absolute left-1 md:left-2 top-[50%] __e-y-[-50%] z-40 group/ep">
            <div class="cursor-pointer flex items-center text-left h-12 sm:h-14 md:h-16 lg:h-18 w-12 md:w-32 lg:w-36">
                <div class="md:group-hover/ep:hidden flex items-center w-full text-accent-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-full md:w-6/12" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                    </svg>
                    <span class="hidden md:block uppercase text-primary-300 md:text-lg w-6/12"
                        x-text="'Ep ' + prevEp.episode_number"></span>
                </div>
                <div class="hidden md:block">
                    <div class="relative hidden group-hover/ep:block">
                        <img class="rounded-sm opacity-75" alt="episode"
                            :src="'https://image.tmdb.org/t/p/w154/' + (prevEp.still_path ?? video_backdrop)">
                        <span
                            class="uppercase text-primary-200 flex items-center justify-center bg-primary-900/50 w-full h-full md:text-lg absolute inset-0"
                            x-text="'Ep ' + prevEp.episode_number"></span>
                    </div>
                </div>
            </div>
        </div>
        <div x-show="nextEp != null" @click="switchEpisode(nextEp)"
            class="hidden group-hover:block absolute right-1 md:right-2 top-[50%] __e-y-[-50%] z-40 group/ep">
            <div class="cursor-pointer flex items-center h-12 sm:h-14 md:h-16 lg:h-18 w-12 md:w-32 lg:w-36">
                <div class="md:group-hover/ep:hidden flex items-center text-right w-full text-accent-400">
                    <span class="hidden md:block uppercase text-primary-300 md:text-lg w-6/12"
                        x-text="'Ep ' + nextEp.episode_number"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-full md:w-6/12" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                    </svg>
                </div>
                <div class="hidden md:block">
                    <div class="relative hidden group-hover/ep:block">
                        <img class="rounded-sm opacity-75" alt="episode"
                            :src="'https://image.tmdb.org/t/p/w154/' + (nextEp.still_path ?? video_backdrop)">
                        <span
                            class="uppercase text-primary-200 flex items-center justify-center bg-primary-900/50 w-full h-full md:text-lg absolute inset-0"
                            x-text="'Ep ' + nextEp.episode_number"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>