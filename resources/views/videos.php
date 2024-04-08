<?php

use App\Core\Configurator;

$this->layout('layout.master')
    ->setupMeta([
        'title' => Configurator::$instance->get('title', 'CoPlay') . ' - ' . Configurator::$instance->get('tagline', 'Stream Free Movies & TV Series Online')
    ])
?>

<main class="container my-5">
    <?php $this
        ->include('includes.intro', [
            'title' => Configurator::$instance->get('intro', 'Welcome to CoPlay, Free Streaming Platform'),
            'description' => Configurator::$instance->get('description', 'Stream Free Movies, TV Series, Anime, and Drama Online with HD Quality. Watch Anywhere Anytime in CoPlay.')
        ])
        ->include('includes.collection', ['collection' => $collection])
        ->include('includes.top', ['top' => $top ?? null])
        ->include('includes.people', ['people' => $people ?? null])
    ?>
</main>