<div x-cloak x-show="serverOpen" x-transition
    class="bg-primary-900 p-4 md:px-8 md:py-6 lg:px-10 lg:py-8 z-50 absolute inset-0 w-full min-h-screen h-max">
    <div class="pb-4 mb-6 border-b border-primary-800">
        <div class="flex items-center justify-between text-primary-100">
            <button @click="serverOpen = false" class="inline-flex items-center hover:text-primary-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z">
                    </path>
                </svg>
                <span class="text-xl"><?= __e('Back To Video') ?></span>
            </button>
            <button @click="serverOpen = false" class="hover:text-primary-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z">
                    </path>
                </svg>
            </button>
        </div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        <template x-for="link in links">
            <?= $template->include('embed/parts/server') ?>
        </template>
    </div>
</div>