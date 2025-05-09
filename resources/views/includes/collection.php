<?php
use Spark\Support\Str;
$tabs = array_keys($collection); ?>
<section x-data="{tabOpen: null}">
    <div class="flex justify-center md:justify-start" x-data="{
        /** time: <?= e(microtime()) ?> */
        init() {
            this.tabOpen = '<?= e($tabs[0]) ?>';
        }
    }">
        <?php
        foreach ($tabs as $tab): ?>
            <button x-on:click="tabOpen = '<?= e($tab) ?>'"
                :class="'border-b-4 text-lg font-semibold ml-4 first:ml-0 ' + (tabOpen === '<?= e($tab) ?>' ? 'text-primary-50 border-accent-400' : 'text-primary-400 hover:text-primary-200 border-transparent')"><?= __(Str::headline($tab)) ?></button>
        <?php endforeach ?>
    </div>
    <?php
    $key = 0;
    foreach ($collection as $tab => $videos): ?>
        <div <?= $key++ > 0 ? 'x-cloak' : '' ?> x-show="tabOpen === '<?= e($tab) ?>'" x-transition
            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            <?php foreach ($videos as $video) {
                echo $view->include('components/video-card', ['video' => $video]);
            } ?>
        </div>
    <?php endforeach ?>
</section>