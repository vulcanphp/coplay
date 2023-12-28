<?php if ($config->is('links')) : ?>
    <section x-cloak x-show="tab == 'links'">
        <div class="flex items-center justify-between mb-4">
            <button @click="newLink()" class="bg-amber-500 hover:bg-amber-600 px-4 py-1 rounded"><?= translate('+ New') ?></button>
            <form>
                <input type="number" name="tmdb" value="<?= input('tmdb') ?>" class="px-4 py-1 bg-primary-700 text-center rounded outline-none focus:outline-amber-400" placeholder="<?= translate('TMDb ID') ?>">
            </form>
        </div>
        <table class="w-full">
            <thead class="bg-primary-900">
                <tr>
                    <th class="px-4 py-2 text-left rounded-tl"><?= translate('TMDb') ?></th>
                    <th class="px-4 py-2 text-left hidden md:table-cell"><?= translate('Server') ?></th>
                    <th class="px-4 py-2 text-left rounded-tr"><?= translate('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$links->hasData()) : ?>
                    <tr>
                        <td class="text-center px-4 py-2 text-gray-300 bg-primary-700 rounded-b" colspan="3"><?= translate('No Links Created') ?></td>
                    </tr>
                <?php endif ?>
                <?php foreach ($links->getData() as $key => $link) : ?>
                    <?php $last = count($links->getData()) == $key + 1 ?>
                    <tr class="bg-primary-700">
                        <td class="px-4 py-2 text-left <?= $last ? 'rounded-bl' : '' ?>">
                            <span class="font-semibold"><?= $link->tmdb ?></span>
                            <span class="italic ml-1">S<?= $link->season ?> E<?= $link->episode ?></span>
                        </td>
                        <td class="px-4 py-2 hidden md:table-cell">
                            <a href="<?= $link->link ?>" target="_blank" class="text-sky-400 hover:text-sky-500"><?= $link->server ?></a>
                        </td>
                        <td class="px-4 py-2 <?= $last ? 'rounded-br' : '' ?>">
                            <button @click='editLink(<?= json_encode($link->toArray()) ?>)' class="text-xs sm:text-sm md:text-base px-2 rounded inline-block bg-yellow-600 hover:bg-yellow-700"><?= translate('Edit') ?></button>
                            <a onclick="return confirm('Are you sure to delete this')" href="?action=delete&id=<?= $link->id ?>" class="text-xs sm:text-sm md:text-base px-2 rounded inline-block bg-rose-600 hover:bg-rose-700 ml-1"><?= translate('Delete') ?></a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?php if ($links->hasLinks()) : ?>
            <div class="flex justify-center mt-2">
                <?= $links->getLinks() ?>
            </div>
        <?php endif ?>
    </section>
<?php endif ?>