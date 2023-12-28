<div x-show="tabOpen == 'clip'" x-cloak class="text-gray-300">
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <?php foreach (array_reverse($video->videos['results'] ?? []) as $clip) : if (strtolower($clip['site'] ?? '') !== 'youtube') continue; ?>
            <div class="mt-2">
                <a @click="isPlaying = true, closePlayer = true, isLoading = true, FrameUrl = 'https://www.youtube.com/embed/<?= $clip['key'] ?>?autoplay=1&color=white'" href="#playing">
                    <div class="relative group">
                        <img src="http://i3.ytimg.com/vi/<?= $clip['key'] ?>/hqdefault.jpg" alt="image" class="rounded mb-1 hover:opacity-75 transition ease-in-out duration-150">
                        <button class="hidden group-hover:block px-2 rounded-md position-center bg-slate-900 z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-amber-400" width="28" height="28" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M7 6v12l10-6z"></path>
                            </svg>
                        </button>
                    </div>
                    <small><?= $clip['name'] ?? 'N/A' ?></small>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</div>