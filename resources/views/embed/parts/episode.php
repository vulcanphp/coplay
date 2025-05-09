<div class="relative rounded-md group/ep" @click="switchEpisode(ep)"
    :class="episode == ep.episode_number ? 'opacity-50 pointer-events-none' : 'cursor-pointer'">
    <div class="position-center z-50">
        <svg x-cloak x-show="episode == ep.episode_number" xmlns="http://www.w3.org/2000/svg"
            class="w-16 h-16 md:w-20 md:h-20 text-primary-100" viewBox="0 0 24 24">
            <path fill="currentColor" d="M8 7h3v10H8zm5 0h3v10h-3z"></path>
        </svg>
        <svg x-cloak x-show="episode != ep.episode_number" xmlns="http://www.w3.org/2000/svg"
            class="hidden group-hover/ep:block w-16 h-16 md:w-20 md:h-20 text-primary-100" viewBox="0 0 24 24">
            <path fill="currentColor" d="M7 6v12l10-6z"></path>
        </svg>
    </div>
    <img class="object-cover w-full rounded-md aspect-video"
        :src="'https://image.tmdb.org/t/p/w300' + (ep.still_path ?? video_backdrop)" alt="episode">
    <div :class="episode != ep.episode_number && 'group-hover/ep:bg-primary-900/30'"
        class="absolute bx-shadow bg-primary-900/15 inset-0 w-full h-full flex justify-end p-2 md:p-3 flex-col rounded-md">
        <p class="text-primary-300 text-sm" x-text="'S'+ep.season_number+' E'+ep.episode_number"></p>
        <p x-text="ep.name" class="text-primary-100 font-semibold"></p>
    </div>
</div>