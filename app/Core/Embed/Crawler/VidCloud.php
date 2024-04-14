<?php

namespace App\Core\Embed\Crawler;

use App\Core\Embed\Includes\EmbedConfigurator;
use VulcanPhp\Core\Helpers\Arr;
use VulcanPhp\Core\Helpers\Str;
use VulcanPhp\EasyCurl\EasyCurl;

class VidCloud
{
    protected array $data = [];
    protected string $type;

    public function __construct(protected string $endpoint, protected EmbedConfigurator $config)
    {
        $this->type = $config->get('type');

        if ($this->type == 'tv') {
            $this->type = Arr::hasAnyValues(
                array_column($config->get('genres'), 'id'),
                [16]
            )
                ? 'anime'
                : 'drama';
        }
    }

    public function getResults(string $keyword): array
    {
        $resp = EasyCurl::setHeaders([
            'Referer' => $this->endpoint
        ])
            ->get(rtrim($this->endpoint, '/') . '/search.html', ['keyword' => $keyword]);
        if (
            $resp->getStatus() == 200
            && preg_match_all(
                '/<li.*?class=".*?video-block.*?".*?>.*?<a.*?href="(.*?)".*?>.*?<div.*?class="name".*?>(.*?)<\/div>.*?<\/a>.*?<\/li>/s',
                $resp->getBody(),
                $results
            )
        ) {
            unset($results[0]);
            return $results;
        }

        return [];
    }

    public function getId(): ?string
    {
        $id = null;

        // Change Original Title to English Title and find anime
        if (in_array($this->type, ['anime'])) {
            // alternative title for anime name
            $altTitle = null;
            // search with translating original anime title
            if ($this->config->get('title') != $this->config->get('original_title')) {
                $this->data['old_title'] = $this->config->get('title');
                $this->config->push('title', $altTitle = $this->getAlternativeTitle());
            }

            $id = $this->crawlForId();

            // search again if anime does not found with original title
            if ($id == null && isset($this->data['old_title'])) {
                $this->config->push('title', $this->data['old_title']);

                $id = $this->crawlForId();

                // search again if the title is too large for title
                if ($id == null) {
                    $id = $this->searchIdWithShortKeyword();
                }

                // search again if the search keyword is too large
                if ($id == null && !$altTitle !== null) {
                    $this->data['old_title'] = $this->config->get('title');
                    $this->config->push('title', $altTitle);
                    $id = $this->searchIdWithShortKeyword();
                }
            }
            // end for anime crawl request
        } elseif (in_array($this->type, ['movie'])) {
            // crawl request for movie
            $id = $this->crawlForId();

            // search movie again if keyword is too large
            if ($id == null) {
                $id = $this->searchIdWithShortKeyword();
            }

            // if movie is anime and does not exists with title then translate original_title and fetch again
            if (
                $id == null
                && Arr::hasAnyValues(array_column($this->config->get('genres'), 'id'), [16])
                && $this->config->get('title') != $this->config->get('original_title')
            ) {
                $this->data['old_title'] = $this->config->get('title');
                $this->config->push('title', $this->getAlternativeTitle());

                $id = $this->crawlForId();

                // search again if anime movie keyword is too large
                if ($id == null) {
                    $id = $this->searchIdWithShortKeyword();
                }
            }
        } else {
            // auto crawl for drama
            $id = $this->crawlForId();

            // search again if the search keyword is too large for movie and drama
            if ($id == null) {
                $id = $this->searchIdWithShortKeyword();
            }
        }

        // return the matched id
        return $id;
    }

    protected function searchIdWithShortKeyword(): ?string
    {
        $keyword = explode(' ', $this->getSearchKeyword());

        if (count($keyword) >= 5) {

            $id = $this->crawlForId(
                $this->getResults(join(' ', array_slice($keyword, 0, 4)))
            );

            if ($id == null) {
                $this->crawlForId(
                    $this->getResults(join(' ', array_slice($keyword, 2, 4)))
                );
            }

            return $id;
        }

        return null;
    }

    protected function crawlForId(?array $result = null)
    {
        if ($result == null) {
            $result = $this->getResults($this->getSearchKeyword());
        }

        if (!empty($result)) {

            $result = array_map(function ($slug, $title) {
                $relevant   = 0;
                $title      = $this->cleanUtf8Text($title);
                $__title    = $this->cleanUtf8Text($this->config->get('title'));
                $slug       = $this->cleanUtf8Text($slug);
                $find       = Str::slugif($__title);

                // check match slug
                if (like_match("%{$find}%", $slug)) {
                    $relevant += 10;
                }

                // check with raw title
                if (Str::pure($title) == Str::pure($__title)) {
                    $relevant += 20;
                }

                // fuzzy search title
                $parts = explode(' ', $__title);
                foreach ($parts as $key => $atp) {
                    if (
                        ($key == 0 && like_match("%{$atp}", $title))
                        || ($key + 1  == count($parts) && like_match("{$atp}%", $title))
                        || (like_match("%{$atp}%", $title))
                    ) {
                        $relevant += 5;
                    }
                }

                // fuzzy search old title
                if (isset($this->data['old_title']) && $__title != $this->data['old_title']) {
                    $parts = explode(' ', $this->data['old_title']);
                    foreach ($parts as $key => $atp) {
                        if (
                            ($key == 0 && like_match("%{$atp}", $title))
                            || ($key + 1  == count($parts) && like_match("{$atp}%", $title))
                            || (like_match("%{$atp}%", $title))
                        ) {
                            $relevant += 5;
                        }
                    }
                }

                // check match year
                if (like_match("%{$this->config->get('year')}%", $slug)) {
                    $relevant += 10;
                }

                // check season slug
                if (
                    $this->config->get('season', 0) > 1
                    && (like_match("%" . Str::slugif($this->getSeasonName()) . "%", $slug)
                        || like_match("%" . strtolower(preg_replace("/[^a-zA-Z0-9]+/", "-", $this->getSeasonName())) . "%", $slug)
                        || like_match("%" . "-season-{$this->config->get('season')}" . "%", $slug)
                        || preg_match('/' . $this->config->get('season') . 'nd-season/i', $slug)
                        || preg_match('/' . $this->config->get('season') . 'rd-season/i', $slug)
                        || preg_match('/' . $this->config->get('season') . 'th-season/i', $slug)
                        || preg_match('/\-' . $this->config->get('season') . '\-/', $slug)
                    )
                ) {
                    $relevant += 15;
                }

                // check season in title
                if (
                    $this->config->get('season', 0) > 1
                    && (like_match("%" . $this->getSeasonName() . "%", $title)
                        || like_match("%" . preg_replace("/[^a-zA-Z0-9]+/", " ", $this->getSeasonName()) . "%", $title)
                        || like_match("%" . 'Season ' . $this->config->get('season') . "%", $title)
                        || preg_match('/' . $this->config->get('season') . 'nd Season/i', $title)
                        || preg_match('/' . $this->config->get('season') . 'rd Season/i', $title)
                        || preg_match('/' . $this->config->get('season') . 'th Season/i', $title)
                        || preg_match('/' . $this->config->get('season') . '/', $title)
                    )
                ) {
                    $relevant += 15;
                }

                // check slug+season+year (for Drama, movie)
                if (
                    in_array($this->type, ['drama', 'movie'])
                    && (like_match(
                        "%{$find}"
                            . ($this->config->get('season', 0) > 1 ? '-' . Str::slug($this->getSeasonName()) : '')
                            . "-{$this->config->get('year')}" . ($this->config->get('episode', 0) >= 1 ? '-episode-' : '')
                            . "%",
                        $slug
                    ) || like_match(
                        "%{$find}"
                            . ($this->config->get('season', 0) > 1 ? '-season-' . $this->config->get('season') : '')
                            . "-{$this->config->get('year')}" . ($this->config->get('episode', 0) >= 1 ? '-episode-' : '')
                            . "%",
                        $slug
                    )
                    )
                ) {
                    $relevant += 20;
                }

                // - priority for episode count
                if (
                    $this->config->get('type') == 'tv'
                    && strpos($slug, '-episode-') !== false
                ) {
                    $total_ep   = intval($this->getTotalEpisodes());
                    $last_ep    = intval(substr($slug, strrpos($slug, '-episode-') + 9));

                    if ($total_ep > 0 && $last_ep > 0) {
                        if ($last_ep == $total_ep) {
                            $relevant += 30;
                        } elseif (($last_ep + 1) == $total_ep || $last_ep == ($total_ep + 1)) {
                            $relevant += 25;
                        } elseif (
                            ($last_ep >= ($total_ep - 2))
                            || ($total_ep > 5 && $last_ep < ($total_ep - 5))
                            || ($total_ep >= $last_ep)
                        ) {
                            $relevant +=  (5 + (($total_ep > $last_ep) ? round(($last_ep / $total_ep), 2) : round(($total_ep / $last_ep), 2)));
                        }
                    }
                }

                // - priority for season title
                if ($this->config->get('type') == 'tv') {
                    $season_title = $this->getSeasonName();
                    if ($season_title !== null) {
                        if (stripos(Str::pure($title), Str::pure($season_title)) !== false) {
                            if (stripos(Str::pure($title), Str::pure($season_title . ' Episode ')) !== false) {
                                $relevant += 10;
                            } else {
                                $relevant += 5;
                            }
                        }

                        foreach (explode(' ', $season_title) as $sst) {
                            if (like_match("%" . Str::pure($sst) . "%", Str::pure($title))) {
                                $relevant += 5;
                            }
                        }
                    }
                }

                // - priority for season 1
                if (
                    in_array($this->type, ['drama', 'anime'])
                    && $this->config->get('season') == 1
                    && stripos($slug, '-season-') === false
                    && stripos($slug, '-episode-') !== false
                ) {
                    if (substr($slug, strrpos($slug, '-episode-') + 9) >= $this->config->get('episode')) {
                        $relevant += 5;
                    }

                    $relevant += 5;
                }

                // priority for -tv-, -movie- (drama, anime)
                if ((in_array($this->type, ['drama', 'anime']) && stripos($slug, '-tv-') !== false)
                    || (in_array($this->type, ['movie']) && stripos($slug, '-movie-') !== false)
                ) {
                    $relevant += 15;
                }

                if ((in_array($this->type, ['drama', 'anime']) && stripos($slug, '-movie-') !== false)
                    || (in_array($this->type, ['movie']) && stripos($slug, '-tv-') !== false)
                ) {
                    $relevant -= 20;
                }

                // for movie
                if (
                    in_array($this->type, ['movie'])
                    && stripos($slug, '-season-') === false
                    && stripos($slug, '-episode-1') !== false
                ) {
                    $relevant += 20;
                }

                // decrease relevant
                if ($relevant > 0) {
                    // priority for unknown text from title
                    $rwt = $title;
                    foreach (explode(' ', $__title) as $tp) {
                        $rwt = str_ireplace($tp, '', $rwt);
                    }

                    if (isset($this->data['old_title'])) {
                        foreach (explode(' ', $this->data['old_title']) as $tp) {
                            $rwt = str_ireplace($tp, '', $rwt);
                        }
                    }

                    $rwt = preg_replace(['/\d{4}/', '/Episode \d/i', '/Season \d/i', '/\d+nd Season/i', '/\d+th Season/i', '/\d+st Season/i'], '', $rwt);
                    if (strlen(trim($rwt)) > 5) {
                        // increase when season as unknown
                        if ($this->config->get('season', 0) > 1 && stripos($slug, '-season-') === false) {
                            $relevant += 5;
                        } else {
                            $relevant -= strlen(trim($rwt)) >= 10 ? 10 : 5;
                        }
                    }

                    if (
                        // ignore for unnecessary texts
                        preg_match('/\(Dub\)| Part /i', $title)

                        // Unknown detect between season and episode
                        || ($this->config->get('season', 0) > 1
                            && !(preg_match('/\-season\-episode\-/', $slug)
                                || (preg_match('/\-season\-\d+\-episode\-/', $slug))
                            ))
                    ) {
                        $relevant -= 5;
                    }
                }

                // for wrong season
                if ($relevant > 10) {
                    if (
                        strpos($slug, '-season-') !== false
                        && (like_match("%" . Str::slugif($this->getSeasonName()) . "%", $slug)
                            || like_match("%" . "-season-{$this->config->get('season')}" . "%", $slug)
                            || preg_match('/' . $this->config->get('season') . 'nd-season/i', $slug)
                            || preg_match('/' . $this->config->get('season') . 'rd-season/i', $slug)
                            || preg_match('/' . $this->config->get('season') . 'th-season/i', $slug)
                            || preg_match('/' . $this->config->get('season') . 'st-season/i', $slug)
                            || preg_match('/\-' . $this->config->get('season') . '\-/i', $slug)
                        ) === false
                        && empty(trim(preg_replace(['/Season \d/i', '/\d+nd Season/i', '/\d+th Season/i', '/\d+st Season/i'], '', $this->getSeasonName())))
                    ) {
                        $relevant -= 10;
                    }
                }

                return [
                    'relevant' => $relevant,
                    'slug' => $slug,
                    'title' => $title,
                ];
            }, $result[1], $result[2]);

            $result = Arr::multisort($result, 'relevant', true);
            $choose = $result[0]['slug'];

            if (in_array($this->type, ['drama', 'anime'])) {
                $last_ep = substr($choose, strrpos($choose, '-episode-') + 9);
                if ($last_ep > 0 && $last_ep < $this->config->get('episode')) {
                    // check with anime parts
                    $eps = $this->mergeSeasonWithPart($last_ep, $choose, array_column($result, 'slug'));
                    if (!empty($eps) && isset($eps[$this->config->get('episode')])) {
                        return $this->scrapeEmbedId($eps[$this->config->get('episode')], false);
                    } else {
                        // check with parted seasons
                        $eps = $this->mergeWithPartedSeason($last_ep, $choose, array_column($result, 'slug'));
                        if (!empty($eps) && isset($eps[$this->config->get('episode')])) {
                            return $this->scrapeEmbedId($eps[$this->config->get('episode')], false);
                        }
                    }
                }
            }

            return $this->scrapeEmbedId($choose);
        }

        return null;
    }

    protected function scrapeEmbedId(string $slug, bool $replace = true): ?string
    {
        if ($replace && $this->config->get('season') !== null && like_match('%-season-%', $slug)) {
            $slug = preg_replace('/-season-\d+/', '-season-' . $this->config->get('season'), $slug);
        }

        if ($replace && $this->config->get('episode') !== null && like_match('%-episode-%', $slug)) {
            $slug = preg_replace('/-episode-\d+/', '-episode-' . $this->config->get('episode'), $slug);
        }

        $resp = EasyCurl::setHeaders([
            'Referer' => $this->endpoint,
        ])->get($this->endpoint . $slug);

        if (
            $resp->getStatus() == 200
            && preg_match('/<iframe.*?src=".*?.php\?id=([^"]+)".*?>/s', $resp->getBody(), $result)
        ) {
            return isset($result[1]) ? (explode('&', $result[1])[0] ?? null) : null;
        }

        return null;
    }

    protected function getSeasonName(): string
    {
        return collect($this->config->getContent()->seasons ?? [])
            ->find([
                'season_number' => intval($this->config->get('season'))
            ])['name'] ?? 'Season ' . $this->config->get('season', 1);
    }

    protected function getTotalEpisodes(): ?int
    {
        $current = $this->config->getContent()->last_episode_to_air ?? $this->config->getContent()->next_episode_to_air;
        $season  = collect($this->config->getContent()->seasons ?? [])
            ->find(['season_number' => (int)$this->config->get('season')]) ?? null;

        if ($season !== null && $current !== null && $season['season_number'] == $current['season_number']) {
            return $current['episode_number'] ?? ($this->config->get('number_of_episodes'));
        }

        return $season['episode_count'] ?? null;
    }

    protected function mergeSeasonWithPart(int $start_ep, string $slug, array $result): array
    {
        $season_part = [];

        foreach ($result as $res) {
            if (!(preg_match('/-part-(\d+)-episode-(\d+)/', $res, $parts) && isset($parts[1]) && isset($parts[2]))) {
                continue;
            }

            if ($parts[2] > 0) {
                $season_part[] = [
                    'part' => intval($parts[1]),
                    'no_eps' => intval($parts[2]),
                    'slug' => $res
                ];
            }
        }

        return $this->serializeSeasonPart($start_ep, $slug, $season_part);
    }

    protected function mergeWithPartedSeason(int $start_ep, string $slug, array $result): array
    {
        $season_part = [];

        foreach ($result as $res) {
            if (
                stripos($res, '-season-') === false
                || !(preg_match('/-episode-(\d+)/', $res, $parts) && isset($parts[1]))
            ) {
                continue;
            }

            if (
                (preg_match('/-season-(\d+)-/', $res, $part) && isset($part[1]))
                || (stripos($res, '-season-episode-') !== false
                    && preg_match('/-.*?(\d+).*?-season-/s', $res, $part)
                    && isset($part[1])
                )
            ) {
                if ($parts[1] > 0) {
                    $season_part[] = [
                        'part' => intval($part[1]),
                        'no_eps' => intval($parts[1]),
                        'slug' => $res
                    ];
                }
            }
        }

        return $this->serializeSeasonPart($start_ep, $slug, $season_part);
    }

    protected function serializeSeasonPart(int $start_ep, string $slug, array $season_part): array
    {
        $season_part = Arr::multisort($season_part, 'part');

        $list = [];
        for ($i = 1; $i <= $start_ep; $i++) {
            $list[$i] = preg_replace('/-episode-\d+/', '-episode-' . $i, $slug);
        }

        foreach ($season_part as $part) {
            for ($i = 1; $i <= $part['no_eps']; $i++) {
                $start_ep++;
                $list[$start_ep] = preg_replace('/-episode-\d+/', '-episode-' . $i, $part['slug']);
            }
        }

        return $list;
    }

    protected function getSearchKeyword(): string
    {
        return trim(
            str_ireplace(
                ['-', '_'],
                ' ',
                str_ireplace(
                    ["`", "~", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "=", "+", "}", "{", "[", "]", '"', "'", ":", ";", "/", "\\", ">", "<", ".", ","],
                    '',
                    $this->config->get('title')
                )
            )
        );
    }

    protected function getAlternativeTitle(): string
    {
        $titles = collect(
            $this->config->getContent()->alternative_titles['results'] ?? []
        )
            ->map(function ($title) {
                $relevant   = 10;
                $title      = $title['title'];

                if (!$this->isEnglish($title)) {
                    return null;
                }

                if (strpos($this->config->get('title'), ' ') !== false && strpos($title, ' ') === false) {
                    $relevant -= 5;
                }

                if (like_match(Str::lowercase(Str::pure($title)) . '%', Str::lowercase(Str::pure($this->config->get('title'))))) {
                    $relevant -= 5;
                }

                return ['title' => $title, 'word_count' => count(explode(' ', $title)), 'strlen' => strlen(Str::pure($title)), 'relevant' => $relevant];
            })
            ->filter();

        // fuzzy search title with original title
        if ($titles->count() >= 4) {
            $titles->map(function ($row) {
                $__title    = $this->cleanUtf8Text($this->getOriginalTitle());
                $title      = $row['title'];
                $relevant   = $row['relevant'];

                if (Str::lowercase(Str::pure($title)) == Str::lowercase(Str::pure($__title))) {
                    $relevant += 30;
                }

                if (like_match('%' . Str::pure($__title) . '%', Str::pure($title))) {
                    $relevant += 20;
                }

                $parts = explode(' ', $__title);
                foreach ($parts as $key => $atp) {
                    if (
                        ($key == 0 && like_match("%{$atp}", $title))
                        || ($key + 1  == count($parts) && like_match("{$atp}%", $title))
                        || (like_match("% {$atp} %", $title))
                    ) {
                        $relevant += 5;
                    }
                }

                return [
                    'title' => $title,
                    'word_count' => $row['word_count'],
                    'strlen' => $row['strlen'],
                    'relevant' => $relevant
                ];
            });
        }

        // sort title
        $titles
            ->multisort('word_count', true)
            ->multisort('strlen')
            ->multisort('relevant', true);

        return ($titles->column('title')[0] ?? $this->getOriginalTitle());
    }

    protected function cleanUtf8Text(string $text): string
    {
        setlocale(LC_CTYPE, 'en_US.UTF8');

        $r = '';
        $s1 = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        for ($i = 0; $i < strlen($s1); $i++) {
            $ch1 = $s1[$i];
            $ch2 = mb_substr($text, $i, 1);

            $r .= $ch1 == '?' ? $ch2 : $ch1;
        }

        return trim(preg_replace('/[\'\`\"]+/i', '', $r));
    }

    protected function translateToEnglish(string $text): string
    {
        return trim(
            join(
                ' ',
                array_unique(
                    array_filter(
                        translator_engine()
                            ->translateFromGoogle($text, 'auto', 'en')[0][1] ?? (array) $text
                    )
                ),
            ),
        );
    }

    protected function getOriginalTitle(): string
    {
        $title = $this->config->get('original_title');

        return $this->data['original_title'] ??= (!$this->isEnglish($title) ? $this->translateToEnglish($title) : $title);
    }

    protected function isEnglish($text): bool
    {
        return mb_strlen($text, 'utf-8') >= (strlen($text) / 2);
    }
}
