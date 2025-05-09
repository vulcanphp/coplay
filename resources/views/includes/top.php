<?php if (isset($top)): ?>
    <section class="mt-8">
        <h2 class="font-semibold text-2xl border-accent-400 border-b-4 inline-block leading-10 mb-2"><?= __('Top Rated') ?>
        </h2>
        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-5">
            <?php foreach ($top as $key => $video) {
                echo $view->include('components/video-list', ['video' => $video, 'key' => ++$key]);
            } ?>
        </div>
    </section>
<?php endif ?>