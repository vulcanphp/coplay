<div class="relative rounded-md group/ep" @click="switchSeason(se)"
    :class="season == se.season_number ? 'pointer-events-none opacity-50' : 'cursor-pointer'">
    <div class="position-center z-50">
        <svg x-cloak x-show="season == se.season_number" xmlns="http://www.w3.org/2000/svg"
            class="w-16 h-16 md:w-20 md:h-20 text-primary-100" viewBox="0 0 24 24">
            <path fill="currentColor" d="M8 7h3v10H8zm5 0h3v10h-3z"></path>
        </svg>
        <svg x-cloak x-show="season != se.season_number" xmlns="http://www.w3.org/2000/svg"
            class="hidden group-hover/ep:block w-16 h-16 md:w-20 md:h-20 text-primary-100" viewBox="0 0 24 24">
            <path fill="currentColor" d="M7 6v12l10-6z"></path>
        </svg>
    </div>
    <img class="object-cover w-full rounded-md"
        :src="'https://image.tmdb.org/t/p/w185' + (se.poster_path ?? video_poster)" alt="season">
    <div :class="season != se.season_number && 'group-hover/ep:bg-primary-900/30'"
        class="absolute bx-shadow bg-primary-900/15 inset-0 w-full h-full flex justify-end p-2 md:p-3 flex-col rounded-md">
        <p class="text-primary-300 text-xs" x-text="'S'+se.season_number"></p>
        <p x-text="se.name" class="text-primary-100 text-sm"></p>
    </div>
</div>