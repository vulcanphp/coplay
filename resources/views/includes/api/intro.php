<div class="text-center">
    <h2 class="text-5xl font-semibold select-none">
        <span class="text-amber-400 block"><?= translate('Fast & Free') ?></span>
        <span class="text-gray-200 block"><?= translate('Video Streaming API') ?></span>
    </h2>
    <p class="text-xl text-gray-300 mt-4"><?= translate('We provide up-to-date working free streaming links for movies and tv series that can be integrated into your website through our embed links API') ?></p>
    <p class="mt-8">
        <?= str_replace('<#>', '<a target="_blank" class="text-amber-400" href="https://www.themoviedb.org/">TMDb</a>', translate('Only <#> ID Supported')) ?>
    </p>
</div>