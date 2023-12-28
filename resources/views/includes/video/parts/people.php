<div x-show="tabOpen == 'people'" x-cloak class="text-gray-300">
    <?php if (isset($video->created_by) && !empty($video->created_by)) : ?>
        <p class="text-gray-400 uppercase mb-1"><?= translate('Created By') ?></p>
        <div class="grid grid-cols-3 sm:grid-cols-5 mb-4 lg:grid-cols-6 xl:grid-cols-7 gap-4">
            <?php foreach ($video->created_by as $creator) : ?>
                <?php $this->component('components.people', ['person' => $creator]) ?>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <p class="text-gray-400 uppercase mb-1"><?= translate('Actors') ?></p>
    <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-4">
        <?php foreach (array_slice($video->credits['cast'] ?? [], 0, 21) as $cast) : ?>
            <?php $this->component('components.people', ['person' => $cast]) ?>
        <?php endforeach ?>
    </div>
    <?php if (isset($video->credits['crew']) && !empty($video->credits['crew'])) : ?>
        <p class="text-gray-400 uppercase mt-4 mb-1"><?= translate('Crew') ?></p>
        <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-4">
            <?php foreach (array_slice($video->credits['crew'] ?? [], 0, 14) as $crew) : ?>
                <?php $this->component('components.people', ['person' => $crew]) ?>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>