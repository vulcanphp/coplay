<div class="bg-primary-900 p-2 rounded-sm mt-2 max-h-[65vh] overflow-y-auto">
    <?php
    if (!empty($results)) {
        foreach ($results as $index => $result) {
            echo $template->include('ajax/parts/card', ['video' => $result, 'index' => $index]);
        }
    } else {
        ?>
        <p class="text-primary-300 px-4 py-2 text-center">
            No Result for "<?= $keyword ?>"
        </p>
        <?php
    }
    ?>
</div>