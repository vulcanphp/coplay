<div class="w-full max-w-lg mx-auto md:max-w-full md:w-8/12 lg:w-9/12">
    <div class="md:pl-5">
        <h2 class="hidden md:block text-2xl font-bold text-center sm:text-left">
            <?= _e($title) ?>
            <span
                class="text-primary-400 font-semibold">(<?= date('Y', strtotime(strval($isTv ? $video->first_air_date : $video->release_date))) ?>)</span>
        </h2>
        <section x-data="{tabOpen: 'details'}" class="mt-5 text-center sm:text-left">
            <span x-init="() => {
                /** time: <?= _e(microtime()) ?> */
                tabOpen = 'details';
            }"></span>

            <?= $template->include('includes/video/parts/tabs') ?>
            <?= $template->include('includes/video/parts/details') ?>
            <?= $template->include('includes/video/parts/season') ?>
            <?= $template->include('includes/video/parts/clip') ?>
            <?= $template->include('includes/video/parts/people') ?>
            <?= $template->include('includes/video/similar') ?>
        </section>
    </div>
</div>