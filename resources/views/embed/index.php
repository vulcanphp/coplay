<?php

use App\Core\Configurator;

$isTv = isset($video->name) && !isset($video->title);
$year = date('Y', strtotime($video->release_date ?? $video->first_air_date));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch <?= $video->title ?? $video->name ?> (<?= $year ?>) Free Online in <?= Configurator::$instance->get('title', 'CoPlay') ?></title>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <?= mixer()
        ->enque('css', resource_url('assets/dist/bundle.min.css'))
        ->deque('css');
    ?>
    <script defer src="<?= resource_url('assets/dist/bundle.min.js') ?>"></script>
</head>

<body class="bg-primary-800 group font-sans text-white relative w-full min-h-screen" x-data='{
        isLoading: false,
        serverOpen: false,
        episodeOpen: false,
        frameUrl: "",
        isTv: <?= $isTv ? 'true' : 'false' ?>,
        links: <?= json_encode($video->links) ?>,
        seasons: <?= $isTv ? json_encode($video->seasons) : '[]' ?>,
        episodes: <?= $isTv ? json_encode($video->episodes) : '[]' ?>,
        season: <?= $isTv ? (input('remember') == 1 ? '$persist(' . $video->season . ').using(sessionStorage).as("se_' . $video->id . '")' : $video->season) : '0' ?>,
        episode: <?= $isTv ? (input('remember') == 1 ? '$persist(' . $video->episode . ').using(sessionStorage).as("se_' . $video->season . '_ep_' . $video->id . '")' : $video->episode) : '0' ?>,
        video_poster: "<?= $video->poster_path ?>",
        video_backdrop: "<?= $video->backdrop_path ?>",
        default_play: <?= json_encode($video->links[0]) ?>,
        embed_url: "<?= url('embed', ['type' => $isTv ? 'tv' : 'movie', 'id' => $video->id]) ?>",
        serverId: <?= input('remember') == 1 ? '$persist("' . $video->links[0]['id'] . '").using(sessionStorage).as("server_' . $video->id . '")' : '"' . $video->links[0]['id'] . '"' ?>,
        remember: <?= input('remember') == 1 ? 'true' : 'false' ?>,
        title: "<?= str_replace(['"', "'"], '', $video->title ?? $video->name) ?>",
        year: "<?= $year ?>",
        nextEp: null,
        prevEp: null,
        playVideo(link){
            this.isLoading = true, this.serverId = link.id, this.setFrameUrl(link.source)
        },
        setFrameUrl(url){
            this.frameUrl = (this.isTv && url.includes("/embed/") && url.includes("?play=") ? (url + "&season=" + this.season + "&episode=" + this.episode) : url)
        },
        switchServer(link){
            (this.serverId != link.id && (this.playVideo(link))),
            this.serverOpen = false
        },
        startCurrentFrame(){
            this.playVideo(this.links.filter((link) => link.id == this.serverId)[0] ?? this.default_play)
        },
        updateEpisodePaginator(){
            this.nextEp = this.episodes.filter((ep) => ep.episode_number == (this.episode + 1))[0] ?? null,
            this.prevEp = this.episodes.filter((ep) => ep.episode_number == (this.episode - 1))[0] ?? null
        },
        switchEpisode(ep){
            (this.episode != ep.episode_number && (this.links = ep.links, this.episode = ep.episode_number, this.default_play = this.links[0], this.startCurrentFrame(), this.updateEpisodePaginator())),
            this.episodeOpen = false
        },
        autoPlay(){
            (this.isTv && (this.links = this.episodes.filter((ep) => ep.episode_number == this.episode)[0].links, this.default_play = this.links[0])),
            (this.isTv && this.updateEpisodePaginator()),
            this.startCurrentFrame()
        },
        switchSeason(se){
            (this.season != se.season_number && (this.season = se.season_number, this.isLoading = true, (window.location = this.embed_url + "?season=" + se.season_number + (this.remember ? "&remember=1" : "")))),
            this.episodeOpen = false
        }
    }'>

    <?php

    // include player controls such as: server and episode button
    $this->include('embed.parts.controls')

        // include multiple server switcher
        ->include('embed.parts.servers')

        // include episode and season switcher
        ->include('embed.parts.playlist')

    ?>

    <!-- @Iframe: Render Embed from External Source -->
    <iframe class="absolute inset-0 w-full h-full" @load="isLoading = false" :src="frameUrl" frameborder="0" scrolling="yes" allowfullscreen></iframe>

    <!-- @start the player -->
    <div x-init="autoPlay()"></div>
</body>

</html>