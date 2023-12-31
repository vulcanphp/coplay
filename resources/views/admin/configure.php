<?php

use App\Core\Configurator;

$this->layout('admin.layout')
    ->block('title', 'Configure - ' . Configurator::$instance->get('title', 'CoPlay'));
?>

<div class="w-full h-screen flex items-center justify-center">
    <form method="post" class="w-10/12 sm:w-8/12 md:w-6/12 lg:w-4/12 xl:w-2/12 text-center">
        <a href="<?= home_url() ?>" class="flex items-center justify-center text-amber-400 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                <path fill="currentColor" d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zm.001 6c-.001 0-.001 0 0 0h-.465l-2.667-4H20l.001 4zM9.536 9 6.869 5h2.596l2.667 4H9.536zm5 0-2.667-4h2.596l2.667 4h-2.596zM4 5h.465l2.667 4H4V5zm0 14v-8h16l.002 8H4z"></path>
                <path fill="currentColor" d="m10 18 5.5-3-5.5-3z"></path>
            </svg>
            <span class="font-semibold text-xl ml-3"><?= Configurator::$instance->get('title', 'CoPlay') ?></span>
        </a>
        <?= csrf() ?>
        <input type="password" required class="w-full px-4 py-2 mb-4 outline-none text-center focus:outline-amber-400 rounded bg-primary-900" placeholder="<?= translate('New Password') ?>" name="password">
        <input type="password" required class="w-full px-4 py-2 mb-4 outline-none text-center focus:outline-amber-400 rounded bg-primary-900" placeholder="<?= translate('Confirm Password') ?>" name="confirm">
        <input type="text" required class="w-full px-4 py-2 mb-4 outline-none text-center focus:outline-amber-400 rounded bg-primary-900" placeholder="<?= translate('TMDb API Read Access Token') ?>" value="<?= Configurator::$instance->get('tmdb', '') ?>" name="tmdb">
        <button class="bg-amber-500 hover:bg-amber-600 px-4 py-2 inline-block mt-4 rounded"><?= translate('Configure') ?></button>
    </form>
</div>