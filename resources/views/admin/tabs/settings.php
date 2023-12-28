<section x-cloak x-show="tab == 'settings'">
    <h2 class="text-2xl font-semibold mb-4"><?= translate('Site Settings') ?></h2>
    <form method="post">
        <?= csrf() ?>
        <input type="hidden" name="action" value="settings">
        <div class="md:flex items-center mb-4">
            <label for="title" class="md:w-3/12 mb-2 md:mb-0 block"><?= translate('Site Title') ?></label>
            <input type="text" name="title" id="title" value="<?= $config->get('title', '') ?>" class="w-full md:w-9/12 px-4 py-2 outline-none focus:outline-amber-400 rounded bg-primary-900" placeholder="CoPlay">
        </div>
        <div class="md:flex items-center mb-4">
            <label for="tagline" class="md:w-3/12 mb-2 md:mb-0 block"><?= translate('Site Tagline') ?></label>
            <input type="text" name="tagline" id="tagline" value="<?= $config->get('tagline', '') ?>" class="w-full md:w-9/12 px-4 py-2 outline-none focus:outline-amber-400 rounded bg-primary-900" placeholder="Stream Free Movies & TV Series Online">
        </div>
        <div class="md:flex items-center mb-4">
            <label for="intro" class="md:w-3/12 mb-2 md:mb-0 block"><?= translate('Site Intro') ?></label>
            <input type="text" name="intro" id="intro" value="<?= $config->get('intro', '') ?>" class="w-full md:w-9/12 px-4 py-2 outline-none focus:outline-amber-400 rounded bg-primary-900" placeholder="Welcome to CoPlay, Free Streaming Platform">
        </div>
        <div class="md:flex items-start mb-4">
            <label for="description" class="md:w-3/12 mb-2 md:mb-0 block"><?= translate('Site Description') ?></label>
            <textarea name="description" id="description" class="w-full md:w-9/12 px-4 py-2 outline-none focus:outline-amber-400 rounded bg-primary-900" placeholder="Stream Free Movies, TV Shows, Anime, and Drama Online with HD Quality. Watch Anywhere Anytime in CoPlay."><?= $config->get('description', '') ?></textarea>
        </div>
        <div class="md:flex items-center mb-4">
            <label for="language" class="md:w-3/12 mb-2 md:mb-0 block"><?= translate('Site Language') ?></label>
            <select name="language" id="language" class="w-full md:w-9/12 px-4 py-2 outline-none focus:outline-amber-400 rounded bg-primary-900">
                <?php foreach ([
                    'en' => 'English',
                    'zh' => 'Chines',
                    'hi' => 'Hindi',
                    'es' => 'Spanish',
                    'fr' => 'French',
                    'ar' => 'Arabic',
                    'bn' => 'Bengali',
                    'pt' => 'Portuguese',
                    'ru' => 'Russian',
                    'ur' => 'Urdu',
                    'id' => 'Indonesian',
                    'de' => 'German',
                    'ja' => 'Japanese',
                    'sw' => 'Swahili',
                    'te' => 'Telugu',
                    'mr' => 'Marathi',
                    'ta' => 'Tamil',
                    'tr' => 'Turkish',
                    'vi' => 'Vietnamese',
                    'ko' => 'Korean',
                    'it' => 'Italian',
                    'yo' => 'Yoruba',
                    'ml' => 'Malayalam',
                    'ha' => 'Hausa',
                    'th' => 'Thai'
                ] as $code => $language) : ?>
                    <option value="<?= $code ?>" <?= $config->get('language', '') == $code ? 'selected' : '' ?>><?= $language ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="md:flex items-center mb-4">
            <label for="copyright" class="md:w-3/12 mb-2 md:mb-0 block"><?= translate('Site Copyright Text') ?></label>
            <input type="text" name="copyright" id="copyright" value="<?= $config->get('copyright', '') ?>" class="w-full md:w-9/12 px-4 py-2 outline-none focus:outline-amber-400 rounded bg-primary-900" placeholder="&copy; 2024 all right reserved.">
        </div>
        <button class="bg-amber-500 hover:bg-amber-600 px-4 py-2 inline-block mt-4 rounded"><?= translate('Save') ?></button>
    </form>
</section>