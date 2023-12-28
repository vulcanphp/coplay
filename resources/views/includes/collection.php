<section x-data="{tabOpen: 0}">
    <div class="flex justify-center md:justify-start">
        <?php

        use VulcanPhp\Core\Helpers\Str;

        foreach (array_keys($collection) as $key => $tab) : ?>
            <button @click="tabOpen = <?= $key ?>" :class="tabOpen == <?= $key ?> ? 'text-white border-amber-400' : 'text-gray-400 hover:text-gray-200 border-transparent'" class="border-b-4 text-lg font-semibold <?= $key > 0 ? 'ml-4' : '' ?>"><?= translate(Str::read($tab)) ?></button>
        <?php endforeach ?>
    </div>
    <?php foreach (array_values($collection) as $key => $videos) : ?>
        <div <?= $key > 0 ? 'x-cloak' : '' ?> x-show="tabOpen == <?= $key ?>" x-transition class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            <?php foreach ($videos as $video) {
                $this->component('components.video-card', ['video' => $video]);
            } ?>
        </div>
    <?php endforeach ?>
</section>