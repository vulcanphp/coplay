<aside x-cloak x-show="menuOpen" x-transition class="w-full md:w-3/12 mb-4 md:mb-0">
    <button @click="tab = 'dashboard'" class="font-semibold flex mx-auto md:mx-0 items-center mb-4" :class="tab == 'dashboard' ? 'text-amber-400' : 'text-gray-300 hover:text-gray-200'">
        <svg xmlns="http://www.w3.org/2000/svg" class="fill-current w-5 mr-2" viewBox="0 0 24 24">
            <path d="M12 4C6.486 4 2 8.486 2 14a9.89 9.89 0 0 0 1.051 4.445c.17.34.516.555.895.555h16.107c.379 0 .726-.215.896-.555A9.89 9.89 0 0 0 22 14c0-5.514-4.486-10-10-10zm5.022 5.022L13.06 15.06a1.53 1.53 0 0 1-2.121.44 1.53 1.53 0 0 1 0-2.561l6.038-3.962a.033.033 0 0 1 .045.01.034.034 0 0 1 0 .035z"></path>
        </svg>
        <span><?= translate('Index') ?></span>
    </button>
    <?php if ($config->is('links')) : ?>
        <button @click="tab = 'links'" class="font-semibold flex mx-auto md:mx-0 items-center mb-4" :class="tab == 'links' ? 'text-amber-400' : 'text-gray-300 hover:text-gray-200'">
            <svg xmlns="http://www.w3.org/2000/svg" class="fill-current w-5 mr-2" viewBox="0 0 24 24">
                <path d="M4 8H2v12a2 2 0 0 0 2 2h12v-2H4z"></path>
                <path d="M20 2H8a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zm-9 12V6l7 4z"></path>
            </svg>
            <span><?= translate('My Links') ?></span>
        </button>
    <?php endif ?>
    <button @click="tab = 'settings'" class="font-semibold flex mx-auto md:mx-0 items-center mb-4" :class="tab == 'settings' ? 'text-amber-400' : 'text-gray-300 hover:text-gray-200'">
        <svg xmlns="http://www.w3.org/2000/svg" class="fill-current w-5 mr-2" viewBox="0 0 24 24">
            <path d="m2.344 15.271 2 3.46a1 1 0 0 0 1.366.365l1.396-.806c.58.457 1.221.832 1.895 1.112V21a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-1.598a8.094 8.094 0 0 0 1.895-1.112l1.396.806c.477.275 1.091.11 1.366-.365l2-3.46a1.004 1.004 0 0 0-.365-1.366l-1.372-.793a7.683 7.683 0 0 0-.002-2.224l1.372-.793c.476-.275.641-.89.365-1.366l-2-3.46a1 1 0 0 0-1.366-.365l-1.396.806A8.034 8.034 0 0 0 15 4.598V3a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v1.598A8.094 8.094 0 0 0 7.105 5.71L5.71 4.904a.999.999 0 0 0-1.366.365l-2 3.46a1.004 1.004 0 0 0 .365 1.366l1.372.793a7.683 7.683 0 0 0 0 2.224l-1.372.793c-.476.275-.641.89-.365 1.366zM12 8c2.206 0 4 1.794 4 4s-1.794 4-4 4-4-1.794-4-4 1.794-4 4-4z"></path>
        </svg>
        <span><?= translate('Settings') ?></span>
    </button>
    <button @click="tab = 'scripts'" class="font-semibold flex mx-auto md:mx-0 items-center mb-4" :class="tab == 'scripts' ? 'text-amber-400' : 'text-gray-300 hover:text-gray-200'">
        <svg xmlns="http://www.w3.org/2000/svg" class="fill-current w-5 mr-2" viewBox="0 0 24 24">
            <path d="m7.375 16.781 1.25-1.562L4.601 12l4.024-3.219-1.25-1.562-5 4a1 1 0 0 0 0 1.562l5 4zm9.25-9.562-1.25 1.562L19.399 12l-4.024 3.219 1.25 1.562 5-4a1 1 0 0 0 0-1.562l-5-4zm-1.649-4.003-4 18-1.953-.434 4-18z"></path>
        </svg>
        <span><?= translate('Scripts') ?></span>
    </button>
</aside>