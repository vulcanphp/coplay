<a :href="video.type + '/-' + video.id">
    <template x-if="video.poster">
        <img :src="'https://image.tmdb.org/t/p/w185' + video.poster" alt="poster"
            class="hover:opacity-75 w-full rounded-md transition ease-in-out duration-150">
    </template>
    <template x-if="!video.poster">
        <div class="text-center text-primary-400 opacity-25">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8/12 m-auto" viewBox="0 0 24 24">
                <path fill="currentColor"
                    d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zm.001 6c-.001 0-.001 0 0 0h-.466l-2.667-4H20l.001 4zm-5.466 0-2.667-4h2.596l2.667 4h-2.596zm-2.404 0H9.535L6.869 5h2.596l2.666 4zM4 5h.465l2.667 4H4V5z">
                </path>
            </svg>
        </div>
    </template>
</a>
<div class="mt-2">
    <a :href="video.type + '/-' + video.id" class="mt-2 hover:text-primary-300"
        x-text="video.title + ' ('+ (video.type == 'movie' ? 'Movie' : 'Tv') +')'"></a>
</div>