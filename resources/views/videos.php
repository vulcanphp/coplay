<?php

$view->layout('layout/master')
    ->set('title', cms('title', 'CoPlay') . ' - ' . cms('tagline', 'Stream Free Movies & TV Series Online'));

?>

<main class="container my-5">
    <?php

    echo $view->include('includes/slider', ['sliderItems' => $sliderItems ?? null]);
    echo $view->include('includes/intro', [
        'title' => cms('intro', 'Welcome to CoPlay, Free Streaming Platform'),
        'description' => cms('description', 'Stream Free Movies, TV Series, Anime, and Drama Online with HD Quality. Watch Anywhere Anytime in CoPlay.')
    ]);

    echo $view->include('includes/collection', ['collection' => $collection]);
    echo $view->include('includes/top', ['top' => $top ?? null]);
    echo $view->include('includes/people', ['people' => $people ?? null]);

    ?>
</main>