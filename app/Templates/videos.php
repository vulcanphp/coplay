<?php

$template->layout('layout/master')
    ->set('title', cms('title', 'CoPlay') . ' - ' . cms('tagline', 'Stream Free Movies & TV Series Online'));

?>

<main class="container my-5">
    <?php

    echo $template->include('includes/slider', ['sliderItems' => $sliderItems ?? null]);
    echo $template->include('includes/intro', [
        'title' => cms('intro', 'Welcome to CoPlay, Free Streaming Platform'),
        'description' => cms('description', 'Stream Free Movies, TV Series, Anime, and Drama Online with HD Quality. Watch Anywhere Anytime in CoPlay.')
    ]);

    echo $template->include('includes/collection', ['collection' => $collection]);
    echo $template->include('includes/top', ['top' => $top ?? null]);
    echo $template->include('includes/people', ['people' => $people ?? null]);

    ?>
</main>