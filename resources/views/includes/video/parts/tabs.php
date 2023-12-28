<div class="flex justify-center sm:justify-start mb-5">
    <button @click="tabOpen = 'details'" :class="tabOpen == 'details' ? 'text-white border-amber-400' : 'text-gray-400 hover:text-gray-200 border-transparent'" class="border-b-4 text-lg font-semibold"><?= translate('Details')?></button>
    <?php if ($isTv && isset($video->seasons)) : ?>
        <button @click="tabOpen = 'season'" :class="tabOpen == 'season' ? 'text-white border-amber-400' : 'text-gray-400 hover:text-gray-200 border-transparent'" class="border-b-4 text-lg font-semibold ml-4"><?= translate('Seasons')?></button>
    <?php endif ?>
    <button @click="tabOpen = 'clip'" :class="tabOpen == 'clip' ? 'text-white border-amber-400' : 'text-gray-400 hover:text-gray-200 border-transparent'" class="border-b-4 text-lg font-semibold ml-4"><?= translate('Clips')?></button>
    <button @click="tabOpen = 'people'" :class="tabOpen == 'people' ? 'text-white border-amber-400' : 'text-gray-400 hover:text-gray-200 border-transparent'" class="border-b-4 text-lg font-semibold ml-4"><?= translate('People')?></button>
</div>