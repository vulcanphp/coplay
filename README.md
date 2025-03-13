# CoPlay - Your Ultimate Entertainment Application

CoPlay is a free PHP application that brings the world of movies, TV series, anime, and drama right to your fingertips. Enjoy a seamless streaming experience without any subscriptions or hidden fees.

[CoPlay Live Demo](http://coplay.evolesoft.com)

![home](https://github.com/user-attachments/assets/d35cd418-f638-4794-9aab-0876a57bde94)
![player](https://github.com/user-attachments/assets/74d96d0d-91a6-4212-88b1-a0e2c65782dd)
![player-playlist](https://github.com/user-attachments/assets/d213f4ec-7ed4-43a6-8a98-1c17912d4bcd)


## Features
- **Extensive Library:** Dive into a vast collection of movies, TV series, anime, and drama spanning various genres and languages.
- **Auto Video Sources:** CoPlay has a Auto Embed Feature which provides streaming links automatically.
- **Regular Updates:** CoPlay provide integrated auto update mechanism for auto embed sources/link.
- **User-Friendly Interface:** Navigate effortlessly with our super slim, intuitive and user-friendly interface.
- **Free Access:** No subscriptions, no hidden fees – CoPlay provides free and unlimited access to a treasure trove of entertainment.

## Getting Started

1. **Download CoPlay:** [CoPlay Latest Version](https://github.com/vulcanphp/coplay/releases/latest)
2. **Unzip:** After downloading the CoPlay zip extract the source files on you project root directory.
3. **Configure:** Go to *bootstrap/env.php* and modify these following configuration. 
    ```php
    # bootstrap/env.php
    return [
        // TMDB API key
        'TMDB_API_KEY' => '{PAST_YOUR_TMDB_API_HERE}', // Get your TMDB API key from https://www.themoviedb.org

        // Site general settings
        'cms' => [
            'title' => '{YOUR_SITE_TITLE}', // Site title
            'tagline' => '{YOUR_SITE_TAGLINE}', // Site tagline
            'intro' => '{YOUR_SITE_INTRO_HEADING}', // Site intro
            'description' => '{YOUR_SITE_DESCRIPTION}', // Site description
            'disclaimer' => '{YOUR_FOOTER_DISCLAIMER_TEXT}', // Site disclaimer notice
            'copyright' => '{YOUR_COPYRIGHT_TEXT}', // Site copyright text

            // Color settings
            'color' => [
                // available colors: https://tailwindcss.com/docs/colors
                'primary' => 'gray', // Primary color (Background and Text)
                'accent' => 'amber', // Accent/Brand color
            ],

            // Site Features
            'features' => [
                'auto_embed' => true, // Enable Auto Embeds support
                'auto_embed_update' => true, // Enable Auto Embeds Update
                'api' => true, // Enable Public API support
            ],
        ],
    ];
   ```
4. **Start the Application:**
    - **Production Server:** For Apache Server Just Hit your domain and it will open the CoPlay Application.
    - **Development Server:**
    ```bash
    # root directory

    php -S localhost:8080 -t public
   ```
   [learn more](https://github.com/vulcanphp/hyper)

**Note:** For Non-Apache Production Server, Make Sure Your Server Redirect all Http Request to public/index.php file.

## Support CoPlay

If you find this project helpful and would like to support its continued development, consider [buying me a coffee](https://www.buymeacoffee.com/vulcandev). Your contribution helps me maintain the project, and dedicate more time to enhancing its features.

### Hire for Freelance Work:
- [Fiverr](https://www.fiverr.com/vulcanphp)
- [Website](https://evolesoft.com/contact)
- *WhatsApp*: +880 1969467747
- *Email*: shahin.moyshan2@gmail.com

## Report an Issue

For additional support, feel free to [open a new issue](https://github.com/vulcanphp/coplay/issues) with a detailed description of the problem you are facing. I will be happy to assist you.

Enjoy your entertainment journey with CoPlay!
