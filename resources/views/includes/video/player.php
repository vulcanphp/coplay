<section class="relative h-72 sm:h-80 md:h-96 lg:h-100 xl:h-110" :class="!isPlaying && 'cursor-pointer'" x-data='{
    /** time: <?= e(microtime()) ?> */
    <?= $isTv ? "season: Alpine.\$persist({$video->number_of_seasons}).using(sessionStorage).as(\"se_{$video->id}\")," : '' ?>
    frameUrl() {
        return "<?= route_url('embed', ['type' => ($isTv ? 'tv' : 'movie'), 'id' => $video->id]) ?>" + "?related=1&remember=1" <?= $isTv ? '+ "&season=" + this.season' : '' ?>
    },
}'>
    <button x-show="!isLoading && !isPlaying" @click="playMovie(frameUrl())" x-cloak
        class="px-2 rounded-md position-center bg-primary-900 shadow-lg shadow-primary-950 z-30">
        <svg xmlns="http://www.w3.org/2000/svg" class="text-accent-400" width="42" height="42" viewBox="0 0 24 24">
            <path fill="currentColor" d="M7 6v12l10-6z"></path>
        </svg>
    </button>
    <div x-show="isLoading" x-cloak class="position-center z-30">
        <svg class="animate-spin -ml-1 mr-3 text-primary-50" xmlns="http://www.w3.org/2000/svg" fill="none" width="40"
            height="40" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
    </div>
    <div :class="isPlaying ? 'bg-primary-900/50' : 'bg-primary-900/20'" @click="!isPlaying && playMovie(frameUrl())"
        class="absolute z-20 inset-0 w-full h-full bx-shadow"></div>
    <img :class="isPlaying && 'sm:blur-sm'" class="absolute inset-0 w-full h-full object-cover z-10"
        src="<?= $video->getImageUrl('w1280') . $video->backdrop_path ?>" alt="">
    <div x-show="isPlaying" x-cloak class="group container px-0 z-20 w-full h-full absolute inset-0">
        <button @click="cancelPlay()" x-show="closePlayer" x-cloak
            class="hidden group-hover:block absolute z-30 top-0 md:top-1 right-2 sm:right-5 md:right-10 p-1 md:p-2 hover:text-primary-300 left-auto text-primary-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
                <path fill="currentColor"
                    d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z">
                </path>
            </svg>
        </button>
        <iframe @load="isLoading = false" :src="FrameUrl" width="100%" height="100%" frameborder="0" scrolling="yes"
            allowfullscreen></iframe>
    </div>
</section>