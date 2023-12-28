<section class="mb-6 text-center md:text-left">
    <?php if (isset($title)) : ?>
        <h2 class="font-semibold text-2xl mb-2"><?= translate($title) ?></h2>
    <?php endif ?>
    <?php if (isset($description)) : ?>
        <p><?= translate($description) ?></p>
    <?php endif ?>
</section>