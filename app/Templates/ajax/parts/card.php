<?php

$slug = url((isset($video['title']) ? 'movie' : 'tv') . '/' . slugify($video['title'] ?? $video['name']) . '-' . $video['id']);
?>
<a href="<?= $slug ?>"
    class="rounded-sm hover:text-primary-300 focus:text-primary-200 focus:outline-none focus:ring-2 focus:ring-accent-500 px-1 py-2 <?= $index > 0 ? 'border-t border-primary-700' : '' ?> flex items-center">
    <?php if (isset($video['poster_path'])): ?>
            <img src="https://image.tmdb.org/t/p/w92<?= $video['poster_path'] ?>" class="w-3/12 object-cover rounded-sm"
                alt="poster">
    <?php else: ?>
            <svg xmlns="http://www.w3.org/2000/svg" class="text-primary-400 opacity-25 w-3/12" viewBox="0 0 24 24">
                <path fill="currentColor"
                    d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zm.001 6c-.001 0-.001 0 0 0h-.466l-2.667-4H20l.001 4zm-5.466 0-2.667-4h2.596l2.667 4h-2.596zm-2.404 0H9.535L6.869 5h2.596l2.666 4zM4 5h.465l2.667 4H4V5z">
                </path>
            </svg>
    <?php endif ?>
    <div class="pl-2 sm:pl-3 w-9/12 text-xs sm:text-sm">
        <p><?= _e($video['title'] ?? ($video['name'] ?? '')) ?></p>
        <small
            class="block text-primary-300">(<?= date('Y', strtotime(strval($video['release_date'] ?? $video['first_air_date']))) ?>)</small>
        <small class="text-xs block"><?= isset($video['title']) ? 'Movie' : 'Tv' ?></small>
    </div>
</a>