# CoPlay - Your Ultimate Entertainment Application

CoPlay is a free PHP application that brings the world of movies, TV series, anime, and drama right to your fingertips. Enjoy a seamless streaming experience without any subscriptions or hidden fees.

[CoPlay Live Demo](http://coplay.free.nf)

![home](https://github.com/vulcanphp/coplay/assets/128284645/5266c5c6-462a-4ba1-8053-d33cf8200995)
![player](https://github.com/vulcanphp/coplay/assets/128284645/f243e5fa-922b-4694-8161-0f3597b65f29)


## Features
- **Extensive Library:** Dive into a vast collection of movies, TV series, anime, and drama spanning various genres and languages.
- **Auto Video Sources:** CoPlay has a Auto Embed Feature which provides streaming links automatically.
- **Easy Admin Panel:** CoPlay is a fully dynamic application that you can control from admin panel.
- **Regular Updates:** CoPlay provide integrated update mechanism for application and embed sources as well.
- **User-Friendly Interface:** Navigate effortlessly with our super slim, intuitive and user-friendly interface.
- **Free Access:** No subscriptions, no hidden fees – CoPlay provides free and unlimited access to a treasure trove of entertainment.

## System Requirements

Before you get started with CoPlay, ensure that your system meets the following requirements:

- **PHP Version:** >= 8.0
- **PHP Extensions:**
  - curl
  - mb_string
  - ext-zip
  - pdo

## Getting Started

1. **Download CoPlay:** [CoPlay Latest Version](https://github.com/vulcanphp/coplay/releases/latest)
2. **Unzip:** After downloading the CoPlay zip extract the source files on you project root directory.
3. **(Optional) Database Setup**: if you want to store custom source links for videos then you need to configure: /config/database.php file
    ```php
    <?php

    return [
        // pdo driver
        'driver' => 'mysql',

        // database charset
        'charset' => 'utf8mb4',
        'collate' => 'utf8mb4_unicode_ci',

        // database configuration
        'name' => '[name]',
        'host' => '[host]',
        'port' => '[port]',
        'user' => '[user]',
        'password' => '[password]',
    ];

   ```
4. **Start the Application:**
    - **Production Server:** For Apache Server Just Hit your domain and it will open the CoPlay Application.
    - **Development Server:**
    ```bash
    cd [your-app]

    php vulcan -s
   ```
   [learn more](https://github.com/vulcanphp/vulcanphp)
5. **CoPlay Setup:** When you Start the Application a Configuration Page will Appear.
    - create a password for your admin panel to login
    - set TMDd [TMDb](https://www.themoviedb.org/signup) Access Token (Api/API Read Access Token)

**Note:** For Non-Apache Production Server Make Sure Your Server Redirect all Http Request to index.php file.

## Support CoPlay

If you find this project helpful and would like to support its continued development, consider [buying me a coffee](https://www.buymeacoffee.com/vulcandev). Your contribution helps me maintain the project, and dedicate more time to enhancing its features.

## Report an Issue

For additional support, feel free to [open a new issue](https://github.com/vulcanphp/coplay/issues) with a detailed description of the problem you are facing. I will be happy to assist you.

Enjoy your entertainment journey with CoPlay!
