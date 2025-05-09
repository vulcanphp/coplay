<?php

use Spark\Support\Str;

$isTv = isset($video->name) && !isset($video->title);
if ($isTv) {
    $slug = url('/tv/' . Str::slug($video->name) . '-' . $video->id);
} else {
    $slug = url('/movie/' . Str::slug($video->title) . '-' . $video->id);
}

?>
<div class="mt-8">
    <a href="<?= $slug ?>">
        <?php if (isset($video->poster_path) && !empty($video->poster_path)): ?>
            <img src="<?= $video->getImageUrl() . $video->poster_path ?>" alt="poster"
                class="hover:opacity-75 w-full rounded-md transition ease-in-out duration-150">
        <?php else: ?>
            <div class="text-center text-primary-400 opacity-25">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8/12 m-auto" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zm.001 6c-.001 0-.001 0 0 0h-.466l-2.667-4H20l.001 4zm-5.466 0-2.667-4h2.596l2.667 4h-2.596zm-2.404 0H9.535L6.869 5h2.596l2.666 4zM4 5h.465l2.667 4H4V5z">
                    </path>
                </svg>
            </div>
        <?php endif ?>
    </a>
    <div class="mt-2">
        <a href="<?= $slug ?>" class="mt-2 hover:text-primary-300"><?= $isTv ? $video->name : $video->title ?></a>
        <div class="flex items-center text-primary-400 text-sm mt-1">
            <svg class="fill-current text-orange-500 w-4" viewBox="0 0 24 24">
                <path
                    d="M17.56 21a1 1 0 01-.46-.11L12 18.22l-5.1 2.67a1 1 0 01-1.45-1.06l1-5.63-4.12-4a1 1 0 01-.25-1 1 1 0 01.81-.68l5.7-.83 2.51-5.13a1 1 0 011.8 0l2.54 5.12 5.7.83a1 1 0 01.81.68 1 1 0 01-.25 1l-4.12 4 1 5.63a1 1 0 01-.4 1 1 1 0 01-.62.18z"
                    data-name="star"></path>
            </svg>
            <span class="ml-1"><?= ceil($video->vote_average * 10) ?>%</span>
            <span
                class="ml-1">(<?= date('Y', strtotime(strval($isTv ? $video->first_air_date : $video->release_date))) ?>)</span>
        </div>
        <div class="text-primary-400 mt-1 text-xs">
            <?= $video->genres(
                fn($genres) => join(
                    ', ',
                    array_map(
                        fn($id, $title) => sprintf(
                            '<a class="hover:text-accent-500" href="%s">%s</a>',
                            route_url(
                                'genre',
                                [
                                    'type' => $isTv ? 'tv' : 'movie',
                                    'slug' => Str::slug($title ?? '') . '-' . $id
                                ]
                            ),
                            $title
                        ),
                        array_keys($genres),
                        array_values($genres)
                    )
                )
            ) ?>
        </div>
    </div>
</div>