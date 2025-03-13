<?php

namespace Lib;

/**
 * Tailwind CSS helper class.
 *
 * Provides static methods for generating Tailwind CSS classes from a given color
 * and shade.
 *
 * @package Backpack\Lib
 */
class TailwindHelper
{
    /**
     * @var array  The Color palette for Tailwind CSS classes.
     * @reference  https://tailwindcss.com/docs/colors
     */
    private const COLOR_PLATTE = [
        'red' => [50 => '254 242 242', 100 => '255 226 226', 200 => '255 201 201', 300 => '255 162 162', 400 => '255 100 103', 500 => '251 44 54', 600 => '231 0 11', 700 => '193 0 7', 800 => '159 7 18', 900 => '130 24 26', 950 => '70 8 9'],
        'orange' => [50 => '255 247 237', 100 => '255 237 212', 200 => '255 215 168', 300 => '255 184 106', 400 => '255 137 4', 500 => '255 105 0', 600 => '245 74 0', 700 => '202 53 0', 800 => '159 45 0', 900 => '126 42 12', 950 => '68 19 6'],
        'amber' => [50 => '255 251 235', 100 => '254 243 198', 200 => '254 230 133', 300 => '255 210 48', 400 => '255 185 0', 500 => '254 154 0', 600 => '225 113 0', 700 => '187 77 0', 800 => '151 60 0', 900 => '123 51 6', 950 => '70 25 1'],
        'yellow' => [50 => '254 252 232', 100 => '254 249 194', 200 => '255 240 133', 300 => '255 223 32', 400 => '253 199 0', 500 => '240 177 0', 600 => '208 135 0', 700 => '166 95 0', 800 => '137 75 0', 900 => '115 62 10', 950 => '67 32 4'],
        'lime' => [50 => '247 254 231', 100 => '236 252 202', 200 => '216 249 153', 300 => '187 244 81', 400 => '154 230 0', 500 => '124 207 0', 600 => '94 165 0', 700 => '73 125 0', 800 => '60 99 0', 900 => '53 83 14', 950 => '25 46 3'],
        'green' => [50 => '240 253 244', 100 => '219 252 231', 200 => '185 248 207', 300 => '123 241 168', 400 => '5 223 114', 500 => '0 201 81', 600 => '0 166 62', 700 => '0 130 54', 800 => '1 102 48', 900 => '13 84 43', 950 => '3 46 21'],
        'emerald' => [50 => '236 253 245', 100 => '208 250 229', 200 => '164 244 207', 300 => '94 233 181', 400 => '0 212 146', 500 => '0 188 125', 600 => '0 153 102', 700 => '0 122 85', 800 => '0 96 69', 900 => '0 79 59', 950 => '0 44 34'],
        'teal' => [50 => '240 253 250', 100 => '203 251 241', 200 => '150 247 228', 300 => '70 237 213', 400 => '0 213 190', 500 => '0 187 167', 600 => '0 150 137', 700 => '0 120 111', 800 => '0 95 90', 900 => '11 79 74', 950 => '2 47 46'],
        'cyan' => [50 => '236 254 255', 100 => '206 250 254', 200 => '162 244 253', 300 => '83 234 253', 400 => '0 211 243', 500 => '0 184 219', 600 => '0 146 184', 700 => '0 117 149', 800 => '0 95 120', 900 => '16 78 100', 950 => '5 51 69'],
        'sky' => [50 => '240 249 255', 100 => '223 242 254', 200 => '184 230 254', 300 => '116 212 255', 400 => '0 188 255', 500 => '0 132 209', 600 => '0 132 209', 700 => '0 105 168', 800 => '0 89 138', 900 => '2 74 113', 950 => '5 47 74'],
        'blue' => [50 => '239 246 255', 100 => '219 234 254', 200 => '190 219 255', 300 => '142 197 255', 400 => '81 162 255', 500 => '43 127 255', 600 => '21 93 252', 700 => '20 71 230', 800 => '25 60 184', 900 => '28 57 142', 950 => '22 37 86'],
        'indigo' => [50 => '238 242 255', 100 => '224 231 255', 200 => '198 210 255', 300 => '163 179 255', 400 => '124 134 255', 500 => '97 95 255', 600 => '79 57 246', 700 => '67 45 215', 800 => '55 42 172', 900 => '49 44 133', 950 => '30 26 77'],
        'violet' => [50 => '245 243 255', 100 => '237 233 254', 200 => '221 214 255', 300 => '196 180 255', 400 => '166 132 255', 500 => '142 81 255', 600 => '127 34 254', 700 => '112 8 231', 800 => '93 14 192', 900 => '77 23 154', 950 => '47 13 104'],
        'purple' => [50 => '250 245 255', 100 => '243 232 255', 200 => '233 212 255', 300 => '218 178 255', 400 => '194 122 255', 500 => '173 70 255', 600 => '152 16 250', 700 => '130 0 219', 800 => '110 17 176', 900 => '89 22 139', 950 => '60 3 102'],
        'fuchsia' => [50 => '253 244 255', 100 => '250 232 255', 200 => '246 207 255', 300 => '244 168 255', 400 => '237 107 255', 500 => '225 42 251', 600 => '200 0 222', 700 => '168 0 183', 800 => '138 1 148', 900 => '114 19 120', 950 => '75 0 79'],
        'pink' => [50 => '253 242 248', 100 => '252 231 243', 200 => '252 206 232', 300 => '253 165 213', 400 => '251 100 182', 500 => '246 51 154', 600 => '230 0 118', 700 => '198 0 92', 800 => '163 0 76', 900 => '134 16 67', 950 => '81 4 36'],
        'rose' => [50 => '255 241 242', 100 => '255 228 230', 200 => '255 204 211', 300 => '255 161 173', 400 => '255 99 126', 500 => '255 32 86', 600 => '236 0 63', 700 => '199 0 54', 800 => '165 0 54', 900 => '139 8 54', 950 => '77 2 24'],
        'slate' => [50 => '248 250 252', 100 => '241 245 249', 200 => '226 232 240', 300 => '202 213 226', 400 => '144 161 185', 500 => '98 116 142', 600 => '69 85 108', 700 => '49 65 88', 800 => '29 41 61', 900 => '15 23 43', 950 => '2 6 24'],
        'gray' => [50 => '249 250 251', 100 => '243 244 246', 200 => '229 231 235', 300 => '209 213 220', 400 => '153 161 175', 500 => '106 114 130', 600 => '74 85 101', 700 => '54 65 83', 800 => '30 41 57', 900 => '16 24 40', 950 => '3 7 18'],
        'zinc' => [50 => '250 250 250', 100 => '244 244 245', 200 => '228 228 231', 300 => '212 212 216', 400 => '159 159 169', 500 => '113 113 123', 600 => '82 82 92', 700 => '63 63 71', 800 => '39 39 42', 900 => '24 24 27', 950 => '9 9 11'],
        'neutral' => [50 => '250 250 250', 100 => '245 245 245', 200 => '229 229 229', 300 => '212 212 212', 400 => '161 161 161', 500 => '115 115 115', 600 => '82 82 82', 700 => '64 64 64', 800 => '38 38 38', 900 => '23 23 23', 950 => '10 10 10'],
        'stone' => [50 => '250 250 249', 100 => '245 245 244', 200 => '231 229 228', 300 => '214 211 209', 400 => '166 160 155', 500 => '121 113 107', 600 => '87 83 77', 700 => '68 64 59', 800 => '41 37 36', 900 => '28 25 23', 950 => '12 10 9'],
    ];

    /**
     * Constructor for the TailwindHelper class.
     *
     * Initializes a new instance of TailwindHelper with the specified configuration.
     *
     * @param array $config Configuration array for initializing the TailwindHelper instance.
     */
    public function __construct(private array $config = [])
    {
        $this->config = array_merge([
            'colors' => [
                'accent' => 'blue',
                'primary' => 'gray',
            ],
            'fonts' => [
                'primary' => 'Inter',
            ],
            'assets' => []
        ], $config);
    }

    /**
     * Adds an asset to the configuration.
     *
     * @param string $type The type of asset to add (e.g. "css", "js").
     * @param string $url The URL of the asset to add.
     */
    public function addAsset(string $type, string $url): void
    {
        $this->config['assets'][$type][] = $url;
    }

    /**
     * Adds a font to the configuration.
     *
     * @param string $id The ID of the font to add (e.g. "primary", "secondary").
     * @param string $name The name of the font to add (e.g. "Inter", "Roboto").
     * @param string|null $fontUrl The URL of the font to add (only required if the font is not a system font).
     */
    public function addFont(string $id, string $name, ?string $fontUrl = null): void
    {
        $this->config['fonts'][$id] = $name;

        // If a font URL is provided, add it to the CSS assets.
        if ($fontUrl) {
            $this->addAsset('style', $fontUrl);
        }
    }

    /**
     * Adds a color to the configuration.
     *
     * @param string $id The ID of the color to add (e.g. "primary", "accent", "danger").
     * @param string $name The name of the color to add (e.g. "blue", "red", "green").
     */
    public function addColor(string $id, string $name): void
    {
        $this->config['colors'][$id] = $name;
    }

    /**
     * Merges the given configuration with the existing configuration for the TailwindHelper instance.
     *
     * @param array $config The configuration array to merge with the existing configuration.
     *
     * @return void
     */
    public function configure(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Retrieves the color value from the COLOR_PLATTE based on the given color name and shade.
     *
     * @param string $name The name of the color to retrieve.
     * @param int $shade The shade of the color to retrieve.
     * @param int $opacity The opacity of the color (default is 100).
     * @return string The hexadecimal color value.
     */
    public function getColor(string $name, int $shade, int $opacity = 100): string
    {
        return sprintf(
            'rgb(%s / %s)',
            ...[self::COLOR_PLATTE[$this->config['colors'][$name]][$shade], $opacity / 100]
        );
    }

    /**
     * Retrieves the font value from the configuration based on the given font name.
     *
     * @param string $name The name of the font to retrieve.
     * @return string The font name.
     */
    public function getFont(string $name): string
    {
        return $this->config['fonts'][$name];
    }

    /**
     * Retrieves the color palette from the COLOR_PLATTE.
     *
     * If a color name is provided, it returns the palette for that specific color.
     * Otherwise, it returns the entire COLOR_PLATTE.
     *
     * @param string $name The name of the color palette to retrieve, or '*' to retrieve all palettes.
     * @return array The color palette(s) as an associative array.
     */
    public function getColorPlatte(string $name = '*'): array
    {
        if ($name === '*') {
            return self::COLOR_PLATTE;
        }

        return self::COLOR_PLATTE[$name];
    }

    /**
     * Generate CSS root variables for colors and fonts.
     *
     * @return string CSS code with color and font variables.
     */
    public function getRoot(): string
    {
        // Initialize the root CSS string
        $root = '';

        // Add color variables to the root string
        foreach ($this->config['colors'] ?? [] as $name => $color) {
            foreach (self::COLOR_PLATTE[$color] ?? [] as $shade => $code) {
                $root .= sprintf('--color-%s-%d: %s;', $name, $shade, $code);
            }
        }

        // Add font variables to the root string
        foreach ($this->config['fonts'] ?? [] as $name => $code) {
            $root .= sprintf('--font-%s: %s;', $name, $code);
        }

        return $root;
    }

    /**
     * Generate JavaScript code to expose colors and fonts as global variables.
     *
     * @return string JavaScript code with color and font variables.
     */
    public function getJs(): string
    {
        // Initialize the colors array
        $colors = [];
        // Populate colors using the configuration
        foreach ($this->config['colors'] ?? [] as $name => $color) {
            foreach (self::COLOR_PLATTE[$color] ?? [] as $varian => $code) {
                $colors[sprintf('%s%d', $name, $varian)] = $code;
            }
        }

        // Initialize the fonts array
        $fonts = [];
        // Populate fonts using the configuration
        foreach ($this->config['fonts'] ?? [] as $name => $code) {
            $fonts[$name] = $code;
        }

        // Function to encode data to JSON format
        $encode = fn(array $data) => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Return JavaScript code as a string
        return sprintf(
            'window.$colors = %s; window.$color = (name, opacity = 100) => `rgb(${window.$colors[name]} / ${opacity / 100})`; window.$fonts = %s;',
            ...[$encode($colors), $encode($fonts)]
        );
    }

    /**
     * Generate HTML code for loading custom assets (CSS and JS files).
     *
     * @return string HTML code with CSS and JS links.
     */
    public function getAssets(): string
    {
        $assets = '';

        // Add links to custom CSS stylesheets
        foreach ($this->config['assets']['style'] ?? [] as $styleLink) {
            $assets .= sprintf(
                '<link rel="stylesheet" href="%s">',
                $styleLink
            );
        }

        // Add links to custom JavaScript files
        foreach ($this->config['assets']['script'] ?? [] as $scriptLink) {
            $assets .= sprintf(
                '<script src="%s" type="text/javascript"></script>',
                $scriptLink
            );
        }

        return $assets;
    }

    /**
     * Get the Tailwind CSS as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '%s<style>[x-cloak] { display: none !important; } :root { %s }</style><script>%s</script>',
            ...[$this->getAssets(), $this->getRoot(), $this->getJs()]
        );
    }
}