<div x-show="tabOpen === 'clip'" x-cloak class="text-primary-300">
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <?php foreach (array_reverse($video->videos['results'] ?? []) as $clip):
            if (!isset($clip['key']) || strtolower($clip['site'] ?? '') !== 'youtube')
                continue; ?>
            <div class="mt-2" x-data="{
                playClip(clip){
                    this.isPlaying = true;
                    this.closePlayer = true;
                    this.isLoading = true;
                    this.FrameUrl = `https://www.youtube.com/embed/${clip}?autoplay=1&color=white`;
                    
                    // scroll to top with animation
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                init() {
                    $el.querySelectorAll('button[clip]').forEach(el => {
                        el.addEventListener('click', () => this.playClip(el.getAttribute('clip')));
                    });
                },
            }">
                <button clip="<?= _e($clip['key']) ?>">
                    <div class="relative group">
                        <img src="http://i3.ytimg.com/vi/<?= _e($clip['key']) ?>/hqdefault.jpg" alt="image"
                            class="rounded-sm hover:opacity-75 transition ease-in-out duration-150">
                        <span class="hidden group-hover:block px-2 rounded-md position-center bg-primary-900 z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-accent-400" width="28" height="28"
                                viewBox="0 0 24 24">
                                <path fill="currentColor" d="M7 6v12l10-6z"></path>
                            </svg>
                        </span>
                    </div>
                    <?php if (isset($clip['name'])): ?>
                        <small class="block mt-1"><?= $clip['name'] ?></small>
                    <?php endif ?>
                </button>
            </div>
        <?php endforeach ?>
    </div>
</div>