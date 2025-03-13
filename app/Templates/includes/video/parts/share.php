<div x-data="{ openShare: false, /** time: <?= _e(microtime()) ?> */ }">
    <button x-on:click="openShare = true" class="text-primary-400 hover:text-primary-300 transition">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24">
            <path fill="currentColor"
                d="M5.5 15a3.51 3.51 0 0 0 2.36-.93l6.26 3.58a3.06 3.06 0 0 0-.12.85 3.53 3.53 0 1 0 1.14-2.57l-6.26-3.58a2.74 2.74 0 0 0 .12-.76l6.15-3.52A3.49 3.49 0 1 0 14 5.5a3.35 3.35 0 0 0 .12.85L8.43 9.6A3.5 3.5 0 1 0 5.5 15zm12 2a1.5 1.5 0 1 1-1.5 1.5 1.5 1.5 0 0 1 1.5-1.5zm0-13A1.5 1.5 0 1 1 16 5.5 1.5 1.5 0 0 1 17.5 4zm-12 6A1.5 1.5 0 1 1 4 11.5 1.5 1.5 0 0 1 5.5 10z">
            </path>
        </svg>
        <span class="text-xs hidden md:block"><?= __e('Share') ?></span>
    </button>
    <div x-cloak x-show="openShare" x-transition
        class="fixed inset-0 w-full h-full bg-primary-950/65 z-40 flex items-center justify-center">
        <div x-on:click.away="openShare = false" x-on:keydown.escape.window="openShare = false"
            class="bg-primary-900 w-10/12 sm:w-8/12 md:w-6/12 lg:w-4/12 xl:w-3/12 px-6 py-5 rounded-xl shadow-md shadow-primary-900/25">
            <div class="flex items-center justify-between text-primary-300">
                <span class="font-semibold text-lg"><?= __e('Share') ?></span>
                <button x-on:click="openShare = false" class="hover:text-primary-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-5 gap-1 sm:gap-2 md:gap-3 my-2">
                <a target="_blank" href="https://www.facebook.com/sharer.php?u=<?= request_url() ?>"
                    class="p-1 block opacity-75 hover:opacity-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="fill-current text-primary-200" viewBox="0 0 24 24">
                        <path
                            d="M12.001 2.002c-5.522 0-9.999 4.477-9.999 9.999 0 4.99 3.656 9.126 8.437 9.879v-6.988h-2.54v-2.891h2.54V9.798c0-2.508 1.493-3.891 3.776-3.891 1.094 0 2.24.195 2.24.195v2.459h-1.264c-1.24 0-1.628.772-1.628 1.563v1.875h2.771l-.443 2.891h-2.328v6.988C18.344 21.129 22 16.992 22 12.001c0-5.522-4.477-9.999-9.999-9.999z">
                        </path>
                    </svg>
                </a>
                <a target="_blank"
                    href="https://twitter.com/share?url=<?= request_url() ?>&text=<?= $video->title ?? $video->name ?>"
                    class="p-1 block opacity-75 hover:opacity-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="fill-current text-primary-200" viewBox="0 0 24 24">
                        <path
                            d="M19.633 7.997c.013.175.013.349.013.523 0 5.325-4.053 11.461-11.46 11.461-2.282 0-4.402-.661-6.186-1.809.324.037.636.05.973.05a8.07 8.07 0 0 0 5.001-1.721 4.036 4.036 0 0 1-3.767-2.793c.249.037.499.062.761.062.361 0 .724-.05 1.061-.137a4.027 4.027 0 0 1-3.23-3.953v-.05c.537.299 1.16.486 1.82.511a4.022 4.022 0 0 1-1.796-3.354c0-.748.199-1.434.548-2.032a11.457 11.457 0 0 0 8.306 4.215c-.062-.3-.1-.611-.1-.923a4.026 4.026 0 0 1 4.028-4.028c1.16 0 2.207.486 2.943 1.272a7.957 7.957 0 0 0 2.556-.973 4.02 4.02 0 0 1-1.771 2.22 8.073 8.073 0 0 0 2.319-.624 8.645 8.645 0 0 1-2.019 2.083z">
                        </path>
                    </svg>
                </a>
                <a target="_blank"
                    href="https://www.linkedin.com/shareArticle?url=<?= request_url() ?>&title=<?= $video->title ?? $video->name ?>"
                    class="p-1 block opacity-75 hover:opacity-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="fill-current text-primary-200" viewBox="0 0 24 24">
                        <circle cx="4.983" cy="5.009" r="2.188"></circle>
                        <path
                            d="M9.237 8.855v12.139h3.769v-6.003c0-1.584.298-3.118 2.262-3.118 1.937 0 1.961 1.811 1.961 3.218v5.904H21v-6.657c0-3.27-.704-5.783-4.526-5.783-1.835 0-3.065 1.007-3.568 1.96h-.051v-1.66H9.237zm-6.142 0H6.87v12.139H3.095z">
                        </path>
                    </svg>
                </a>
                <a target="_blank"
                    href="https://www.tumblr.com/share/link?url=<?= request_url() ?>&name=<?= $video->title ?? $video->name ?>"
                    class="p-1 block opacity-75 hover:opacity-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="fill-current text-primary-200" viewBox="0 0 24 24">
                        <path
                            d="M14.078 20.953c-2.692 0-4.699-1.385-4.699-4.7v-5.308H6.931V8.07c2.694-.699 3.821-3.017 3.95-5.023h2.796v4.558h3.263v3.34h-3.263v4.622c0 1.386.699 1.864 1.813 1.864h1.58v3.522h-2.992z">
                        </path>
                    </svg>
                </a>
                <a target="_blank"
                    href="https://reddit.com/submit?url=<?= request_url() ?>&title=<?= $video->title ?? $video->name ?>"
                    class="p-1 block opacity-75 hover:opacity-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="fill-current text-primary-200" viewBox="0 0 24 24">
                        <circle cx="9.67" cy="13" r="1.001"></circle>
                        <path
                            d="M14.09 15.391A3.28 3.28 0 0 1 12 16a3.271 3.271 0 0 1-2.081-.63.27.27 0 0 0-.379.38c.71.535 1.582.809 2.471.77a3.811 3.811 0 0 0 2.469-.77v.04a.284.284 0 0 0 .006-.396.28.28 0 0 0-.396-.003zm.209-3.351a1 1 0 0 0 0 2l-.008.039c.016.002.033 0 .051 0a1 1 0 0 0 .958-1.038 1 1 0 0 0-1.001-1.001z">
                        </path>
                        <path
                            d="M12 2C6.479 2 2 6.477 2 12c0 5.521 4.479 10 10 10s10-4.479 10-10c0-5.523-4.479-10-10-10zm5.859 11.33c.012.146.012.293 0 .439 0 2.24-2.609 4.062-5.83 4.062s-5.83-1.82-5.83-4.062a2.681 2.681 0 0 1 0-.439 1.46 1.46 0 0 1-.455-2.327 1.458 1.458 0 0 1 2.063-.063 7.145 7.145 0 0 1 3.899-1.23l.743-3.47v-.004A.313.313 0 0 1 12.82 6l2.449.49a1.001 1.001 0 1 1-.131.61L13 6.65l-.649 3.12a7.123 7.123 0 0 1 3.85 1.23 1.46 1.46 0 0 1 2.469 1c.01.563-.307 1.08-.811 1.33z">
                        </path>
                    </svg>
                </a>
            </div>
            <?php
            if (is_feature_enabled('api')): ?>
                <textarea
                    x-on:click="navigator.clipboard.writeText($refs.embed.value) && alert('<?= __e('Embed Copied') ?>');"
                    x-ref="embed"
                    class="cursor-pointer hover:bg-primary-800 mt-4 w-full h-24 rounded-md outline-none bg-primary-800/75 resize-none p-4 text-sm"><iframe src="<?= route_url('embed', ['type' => ($isTv ? 'tv' : 'movie'), 'id' => $video->id]) ?>" width="100%" height="100%" frameborder="0" scrolling="no" allowfullscreen></iframe></textarea>
            <?php endif ?>
        </div>
    </div>
</div>