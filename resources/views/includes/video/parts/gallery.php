<p class="text-gray-400 uppercase mt-4 mb-1"><?= translate('Photos') ?></p>
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" x-data="{ isOpen: false, image: ''}">
    <?php foreach (array_slice($video->images['backdrops'] ?? [], 0, 16) as $photo) : ?>
        <div class="mt-2">
            <a @click.prevent="isOpen = true, image='<?= $video->getImageUrl('w1280') . $photo['file_path'] ?>'" href="#">
                <img src="<?= $video->getImageUrl('w300') . $photo['file_path'] ?>" alt="image1" class="rounded hover:opacity-75 transition ease-in-out duration-150">
            </a>
        </div>
    <?php endforeach ?>
    <div class="fixed z-30 bg-primary-900/25 top-0 left-0 w-full h-full flex items-center shadow-lg overflow-y-auto" x-cloak x-show="isOpen" x-transition>
        <div class="container mx-auto lg:px-32 rounded-lg overflow-y-auto">
            <div class="bg-primary-800 rounded shadow-md shadow-primary-900/25">
                <div class="flex justify-center pt-2">
                    <button @click="isOpen = false" @keydown.escape.window="isOpen = false" class="text-4xl leading-none text-gray-200 hover:text-gray-300">×</button>
                </div>
                <div class="p-4">
                    <img :src="image" alt="poster" class="rounded">
                </div>
            </div>
        </div>
    </div>
</div>