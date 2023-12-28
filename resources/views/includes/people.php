<?php if (isset($people)) : ?>
    <section class="mt-8">
        <h2 class="font-semibold text-2xl border-amber-400 border-b-4 inline-block leading-10 mb-2"><?= translate('Popular People') ?></h2>
        <div class="mt-3 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <?php foreach ($people as $person) {
                $this->component('components.person', ['person' => $person]);
            } ?>
        </div>
    </section>
<?php endif ?>