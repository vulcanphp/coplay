<div class="bg-slate-800 p-2 rounded mt-2 <?= count($results) >= 4 ? 'max-h-[75vh] overflow-y-scroll' : '' ?>">
    <?php
    if (!empty($results)) {
        foreach ($results as $index => $result) {
            $this->include('ajax.parts.card', ['video' => $result, 'index' => $index]);
        }
    } else {
    ?>
        <p class="text-gray-300 px-4 py-2 text-center">
            No Result for "<?= $keyword ?>"
        </p>
    <?php
    }
    ?>
</div>