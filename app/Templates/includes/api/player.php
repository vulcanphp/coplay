<section class="text-center mt-4">
    <div class="flex justify-center">
        <button @click="switchTab('movie')"
            :class="tabOpen == 'movie' ? 'text-primary-50 border-accent-400' : 'text-primary-400 hover:text-primary-200 border-transparent'"
            class="border-b-4 text-lg font-semibold"><?= __e('Movie') ?></button>
        <button @click="switchTab('tv')"
            :class="tabOpen == 'tv' ? 'text-primary-50 border-accent-400' : 'text-primary-400 hover:text-primary-200 border-transparent'"
            class="border-b-4 text-lg font-semibold ml-4"><?= __e('Tv Series') ?></button>
    </div>
    <div x-cloak x-show="tabOpen == 'movie'" class="mt-6">
        <input type="text" @change="initFrame()" x-model="tmdb"
            class="w-4/12 text-center p-2 bg-primary-900 border-2 font-semibold border-primary-800 rounded outline-none focus:border-accent-400 text-primary-200">
    </div>
    <div x-cloak x-show="tabOpen == 'tv'" class="mt-6 flex">
        <input type="text" @change="initFrame()" x-model="tmdb"
            class="w-4/12 text-center p-2 bg-primary-900 border-2 font-semibold border-primary-800 rounded-l outline-none focus:border-accent-400 text-primary-200">
        <input type="number" @change="initFrame()" x-model="season"
            class="w-4/12 text-center p-2 bg-primary-900 border-2 font-semibold border-primary-800 outline-none focus:border-accent-400 text-primary-200">
        <input type="number" @change="initFrame()" x-model="episode"
            class="w-4/12 text-center p-2 bg-primary-900 border-2 font-semibold border-primary-800 rounded-r outline-none focus:border-accent-400 text-primary-200">
    </div>
    <div class="mt-4">
        <input type="text" readonly x-model="frame"
            class="px-4 text-center py-2 bg-primary-900 w-full border-2 focus:border-accent-400 font-semibold border-primary-800 rounded outline-none text-primary-200">
        <iframe :src="frame" class="w-full rounded bg-primary-900 h-64 md:h-72 lg:h-80 xl:h-96 mt-4"
            frameborder="0"></iframe>
    </div>
    <span x-init="initFrame()"></span>
</section>