<div class="w-full max-w-lg mx-auto md:max-w-full md:w-8/12 lg:w-9/12">
    <div class="md:pl-5">
        <h2 class="hidden md:block text-2xl font-bold text-center sm:text-left">
            <?= e($title) ?>
            <span
                class="text-primary-400 font-semibold">(<?= date('Y', strtotime(strval($isTv ? $video->first_air_date : $video->release_date))) ?>)</span>
        </h2>
        <section x-data="{tabOpen: 'details'}" class="mt-5 text-center sm:text-left">
            <span x-init="() => {
                /** time: <?= e(microtime()) ?> */
                tabOpen = 'details';
            }"></span>

            <?= $view->include('includes/video/parts/tabs') ?>
            <?= $view->include('includes/video/parts/details') ?>
            <?= $view->include('includes/video/parts/season') ?>
            <?= $view->include('includes/video/parts/clip') ?>
            <?= $view->include('includes/video/parts/people') ?>
            <?= $view->include('includes/video/similar') ?>
        </section>
    </div>
</div>