<?php if ($isTv && isset($video->seasons)): ?>
    <div x-show="tabOpen === 'season'" x-cloak class="text-primary-300">
        <?php foreach ($video->seasons as $season): ?>
            <p class="text-primary-400 uppercase mb-1"><?= $season['name'] ?></p>
            <?php if (isset($season['poster_path'])): ?>
                <img src="<?= $video->getImageUrl('w154') . $season['poster_path'] ?>" alt="image1"
                    class="mb-1 rounded-sm mx-auto sm:mx-0">
            <?php endif ?>
            <?php if (isset($season['air_date'])): ?>
                <p class="mb-1"><?= date('d M, Y', strtotime($season['air_date'])) ?></p>
            <?php endif ?>
            <p class="text-primary-200 text-sm mb-4"><?= $season['overview'] ?? '' ?></p>
        <?php endforeach ?>
    </div>
<?php endif ?>