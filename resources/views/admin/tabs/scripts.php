<section x-cloak x-show="tab == 'scripts'">
    <h2 class="text-2xl font-semibold mb-4"><?= translate('Global Scripts')?></h2>
    <form method="post">
        <?= csrf() ?>
        <input type="hidden" name="action" value="scripts">
        <div class="md:flex items-start mb-4">
            <label for="head" class="md:w-3/12 block mb-2 md:mb-0"><?= translate('Head Tag')?></label>
            <textarea name="head" id="head" class="w-full md:w-9/12 px-4 py-2 outline-none focus:outline-amber-400 rounded bg-primary-900" placeholder="<?= translate('Input Html')?>"><?= $config->get('head', '') ?></textarea>
        </div>
        <div class="md:flex items-start mb-4">
            <label for="body" class="md:w-3/12 block mb-2 md:mb-0"><?= translate('Body Tag')?></label>
            <textarea name="body" id="body" class="w-full md:w-9/12 px-4 py-2 outline-none focus:outline-amber-400 rounded bg-primary-900" placeholder="<?= translate('Input Html')?>"><?= $config->get('body', '') ?></textarea>
        </div>
        <div class="md:flex items-start mb-4">
            <label for="footer" class="md:w-3/12 block mb-2 md:mb-0"><?= translate('Footer Tag')?></label>
            <textarea name="footer" id="footer" class="w-full md:w-9/12 px-4 py-2 outline-none focus:outline-amber-400 rounded bg-primary-900" placeholder="<?= translate('Input Html')?>"><?= $config->get('footer', '') ?></textarea>
        </div>
        <button class="bg-amber-500 hover:bg-amber-600 px-4 py-2 inline-block mt-4 rounded"><?= translate('Save')?></button>
    </form>
</section>