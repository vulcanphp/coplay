<footer class="container">
    <div class="flex py-5 flex-col md:flex-row items-center justify-between">
        <div class="text-sm"><?= $config->get('copyright', translate('&copy; ' . date('Y') . ' all right reserved.')) ?></div>
        <a href="<?= home_url() ?>" class="flex items-center justify-center text-amber-400 my-3 md:my-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24">
                <path fill="currentColor" d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zm.001 6c-.001 0-.001 0 0 0h-.465l-2.667-4H20l.001 4zM9.536 9 6.869 5h2.596l2.667 4H9.536zm5 0-2.667-4h2.596l2.667 4h-2.596zM4 5h.465l2.667 4H4V5zm0 14v-8h16l.002 8H4z"></path>
                <path fill="currentColor" d="m10 18 5.5-3-5.5-3z"></path>
            </svg>
            <span class="font-semibold text-lg ml-2"><?= $config->get('title', 'CoPlay') ?></span>
        </a>
        <p class="text-xs">
            <?= str_replace(['<#>', '<##>'], ['<a href="https://www.themoviedb.org/" class="underline hover:text-gray-300">TMDb API</a>', '<a href="https://github.com/vulcanphp" class="underline hover:text-gray-300">VulcanPhp</a>'], translate('Powered by <#> & <##>')) ?>
        </p>
    </div>
</footer>