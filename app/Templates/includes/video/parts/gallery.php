<p class="text-primary-400 uppercase mt-4 mb-1"><?= __e('Photos') ?></p>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" x-data="{ isOpen: false, image: ''}">
    <?php foreach (array_slice($video->images['backdrops'] ?? [], 0, 16) as $photo): ?>
        <button x-on:click.prevent="isOpen = true, image='<?= $video->getImageUrl('w1280') . $photo['file_path'] ?>'">
            <img src="<?= $video->getImageUrl('w300') . $photo['file_path'] ?>" alt="image1"
                class="rounded hover:opacity-75 transition ease-in-out duration-150">
        </button>
    <?php endforeach ?>
    <div class="fixed z-30 bg-primary-950/65 top-0 left-0 w-full h-full flex items-center shadow-lg overflow-y-auto"
        x-cloak x-show="isOpen" x-transition>
        <div class="container mx-auto lg:px-32 rounded-lg overflow-y-auto">
            <div class="bg-primary-900 rounded shadow-md shadow-primary-900/25 relative"
                x-on:click.away="isOpen = false">
                <div class="p-2">
                    <img :src="image" alt="poster" class="rounded">
                </div>
                <button x-on:click="isOpen = false" x-on:keydown.escape.window="isOpen = false"
                    class="text-4xl leading-none text-primary-200/65 p-2 hover:text-primary-100 absolute top-0 right-2">×</button>
            </div>
        </div>
    </div>
</div>