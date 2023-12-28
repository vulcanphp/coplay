<?php

use App\Core\Configurator;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->getBlock('title', 'Admin - ' . Configurator::$instance->get('title', 'CoPlay')) ?></title>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <?= mixer()
        ->enque('css', resource_url('assets/dist/bundle.min.css'))
        ->deque('css')
    ?>
    <?= mixer()
        ->enque('js', resource_url('assets/dist/bundle.min.js'))
        ->deque('js')
    ?>
</head>

<body class="bg-primary-800 font-sans text-white">

    {{content}}

</body>

</html>