<?php $tabs = array_keys($collection) ?>
<section x-data="{tabOpen: null}">
    <div class="flex justify-center md:justify-start" x-data="{
        /** time: <?= _e(microtime()) ?> */
        init() {
            this.tabOpen = '<?= _e($tabs[0]) ?>';
        }
    }">
        <?php
        foreach ($tabs as $tab): ?>
            <button x-on:click="tabOpen = '<?= _e($tab) ?>'"
                :class="'border-b-4 text-lg font-semibold ml-4 first:ml-0 ' + (tabOpen === '<?= _e($tab) ?>' ? 'text-primary-50 border-accent-400' : 'text-primary-400 hover:text-primary-200 border-transparent')"><?= __(pretty_text($tab)) ?></button>
        <?php endforeach ?>
    </div>
    <?php
    $key = 0;
    foreach ($collection as $tab => $videos): ?>
        <div <?= $key++ > 0 ? 'x-cloak' : '' ?> x-show="tabOpen === '<?= _e($tab) ?>'" x-transition
            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            <?php foreach ($videos as $video) {
                echo $template->include('components/video-card', ['video' => $video]);
            } ?>
        </div>
    <?php endforeach ?>
</section>