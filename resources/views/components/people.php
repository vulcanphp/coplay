<?php
use Spark\Support\Str;
?>
<div class="mt-1 text-center">
    <a
        href="<?= route_url('people', ['slug' => Str::slug(($person['original_name'] ?? $person['name']) . '-' . $person['id'])]) ?>">
        <?php if (isset($person['profile_path'])): ?>
            <img src="https://image.tmdb.org/t/p/w92<?= $person['profile_path'] ?>" alt="actor"
                class="mx-auto mb-1 hover:opacity-75 transition ease-in-out duration-150 rounded-full h-20 w-20 object-cover">
        <?php else: ?>
            <svg xmlns="http://www.w3.org/2000/svg" class="opacity-50 h-20 w-20 mx-auto" viewBox="0 0 24 24">
                <path fill="currentColor"
                    d="M12 2C6.579 2 2 6.579 2 12s4.579 10 10 10 10-4.579 10-10S17.421 2 12 2zm0 5c1.727 0 3 1.272 3 3s-1.273 3-3 3c-1.726 0-3-1.272-3-3s1.274-3 3-3zm-5.106 9.772c.897-1.32 2.393-2.2 4.106-2.2h2c1.714 0 3.209.88 4.106 2.2C15.828 18.14 14.015 19 12 19s-3.828-.86-5.106-2.228z">
                </path>
            </svg>
        <?php endif ?>
        <small class="text-xs leading-3"><?= e($person['original_name'] ?? $person['name']) ?></small>
    </a>
</div>