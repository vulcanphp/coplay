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
3. **Install:** Run the following spark commands to create a *env.php* file and generate a app key. 
    ```php
    // Install dependencies 
    composer install

    // create a env.php file
    php spark config:copy

    // generate application encryption key
    php spark key:generate
   ```
4. **Configuation:** open env.php file and replace TMDB Api Key and also theme and CMS settings.
5. **Start the Application:**
    - **Production Server:** For Apache Server Just Hit your domain and it will open the CoPlay Application.
    - **Development Server:**
    ```bash
    # root directory

    php spark serve
   ```
   [learn more about the core](https://tinymvc.github.io)

**Note:** For Non-Apache Production Server, Make Sure Your Server Redirect all Http Request to public folder.

## Report an Issue
For additional support, feel free to [open a new issue](https://github.com/vulcanphp/coplay/issues) with a detailed description of the problem you are facing. I will be happy to assist you.

Enjoy your entertainment journey with CoPlay!
