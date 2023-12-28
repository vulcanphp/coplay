<?php if ($config->is('links')) : ?>
    <section x-cloak x-show="tab == 'video'">
        <div class="text-center w-full sm:w-10/12 md:w-6/12 mx-auto">
            <h2 class="font-semibold text-2xl mb-4"><?= translate('Add/Edit Video Link')?></h2>
            <form method="post">
                <?= csrf() ?>
                <input type="hidden" name="action" value="link">
                <input type="hidden" name="id" x-model="link.id">
                <input type="number" name="tmdb" x-model="link.tmdb" class="mb-4 w-full px-4 py-2 outline-none text-center focus:outline-amber-400 rounded bg-primary-900" placeholder="<?= translate('TMDb ID')?>">
                <div class="flex mb-4">
                    <div class="w-6/12 pr-3">
                        <input type="number" name="season" x-model="link.season" class="w-full px-4 py-2 outline-none text-center focus:outline-amber-400 rounded bg-primary-900" placeholder="<?= translate('Season')?>">
                    </div>
                    <div class="w-6/12 pl-3">
                        <input type="number" name="episode" x-model="link.episode" class="w-full px-4 py-2 outline-none text-center focus:outline-amber-400 rounded bg-primary-900" placeholder="<?= translate('Episode')?>">
                    </div>
                </div>
                <input type="text" name="server" x-model="link.server" class="mb-4 w-full px-4 py-2 outline-none text-center focus:outline-amber-400 rounded bg-primary-900" placeholder="<?= translate('Server Name')?>">
                <input type="text" name="link" x-model="link.link" class="mb-4 w-full px-4 py-2 outline-none text-center focus:outline-amber-400 rounded bg-primary-900" placeholder="<?= translate('Video Url/Link')?>">
                <button class="bg-amber-500 hover:bg-amber-600 px-4 py-2 inline-block mt-4 rounded"><?= translate('Save')?></button>
            </form>
        </div>
    </section>
<?php endif ?>