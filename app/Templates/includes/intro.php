<section class="mb-6 text-center md:text-left">
    <?php if (isset($title)): ?>
        <h2 class="font-semibold text-2xl"><?= __e($title) ?></h2>
    <?php endif ?>
    <?php if (isset($description)): ?>
        <p class="mt-2"><?= __e($description) ?></p>
    <?php endif ?>
</section>