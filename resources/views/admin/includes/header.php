<?php

use App\Core\Configurator;
?>
<header class="w-full flex justify-between md:justify-center py-2 md:py-4">
    <a href="<?= home_url() ?>" class="flex items-center justify-center text-amber-400">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
            <path fill="currentColor" d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zm.001 6c-.001 0-.001 0 0 0h-.465l-2.667-4H20l.001 4zM9.536 9 6.869 5h2.596l2.667 4H9.536zm5 0-2.667-4h2.596l2.667 4h-2.596zM4 5h.465l2.667 4H4V5zm0 14v-8h16l.002 8H4z"></path>
            <path fill="currentColor" d="m10 18 5.5-3-5.5-3z"></path>
        </svg>
        <span class="font-semibold text-xl ml-3"><?= Configurator::$instance->get('title', 'CoPlay') ?></span>
    </a>
    <button @click="menuOpen = !menuOpen" class="md:hidden mt-3 p-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24">
            <path fill="currentColor" d="M4 6h16v2H4zm4 5h12v2H8zm5 5h7v2h-7z"></path>
        </svg>
    </button>
</header>