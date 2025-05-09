<div class="flex justify-center sm:justify-start mb-5">
    <button x-on:click="tabOpen = 'details'"
        :class="'border-b-4 text-lg font-semibold ' + (tabOpen === 'details' ? 'text-primary-50 border-accent-400' : 'text-primary-400 hover:text-primary-200 border-transparent')"><?= __e('Details') ?></button>
    <?php if ($isTv && isset($video->seasons)): ?>
        <button x-on:click="tabOpen = 'season'"
            :class="'border-b-4 text-lg font-semibold ml-4 ' + (tabOpen === 'season' ? 'text-primary-50 border-accent-400' : 'text-primary-400 hover:text-primary-200 border-transparent')"><?= __e('Seasons') ?></button>
    <?php endif ?>
    <button x-on:click="tabOpen = 'clip'"
        :class="'border-b-4 text-lg font-semibold ml-4 ' + (tabOpen === 'clip' ? 'text-primary-50 border-accent-400' : 'text-primary-400 hover:text-primary-200 border-transparent')"><?= __e('Clips') ?></button>
    <button x-on:click="tabOpen = 'people'"
        :class="'border-b-4 text-lg font-semibold ml-4 ' + (tabOpen === 'people' ? 'text-primary-50 border-accent-400' : 'text-primary-400 hover:text-primary-200 border-transparent')"><?= __e('People') ?></button>
</div>