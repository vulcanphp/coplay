<?php if (isset($similar) && !empty($similar)): ?>
    <div class="mt-10 text-left">
        <h2 class="font-semibold text-xl md:text-2xl border-accent-400 border-b-4 inline-block leading-10 mb-2">
            <?= __e($isTv ? 'Recommended Tv Series' : 'Recommended Movies') ?>
        </h2>
        <div class="mt-3 grid grid-cols-1 lg:grid-cols-2 gap-5">
            <?php foreach (array_slice($similar, 0, 20) as $video) {
                echo $template->include('components/video-list', ['video' => $video]);
            } ?>
        </div>
    </div>
<?php endif ?>