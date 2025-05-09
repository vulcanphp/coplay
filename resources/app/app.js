/**
 * aap.js
 * 
 * This file is the entry point of the Vite application. It contains the
 * necessary code to initialize the application and mount the root
 * component to the DOM.
 *
 * When the application is built, Vite uses this file as the input and
 * generates a bundle that can be loaded by the browser.
 *
 * @module aap
 */

import './app.css';

import Alpine from 'alpinejs';

import axios from 'axios';

import persist from '@alpinejs/persist';
import fireline from 'fireline';
import NProgress from 'nprogress';
import focus from '@alpinejs/focus';

document.addEventListener('alpine:init', () => {

    // Add TipTap Editor as Alpine.js component
    let Swiper, SwiperModules;

    const includeSwiperLibrary = async () => {
        if (!Swiper) {
            // import swiper
            Swiper = (await import('swiper')).Swiper;

            // import swiper modules
            const modules = await import('swiper/modules');
            SwiperModules = [modules.Navigation, modules.Pagination, modules.Autoplay, modules.Parallax, modules.EffectFade, modules.Controller];

            // import swiper styles
            await import('swiper/css');
            await import('swiper/css/navigation');
            await import('swiper/css/pagination');
            await import('swiper/css/autoplay');
            await import('swiper/css/parallax');
            await import('swiper/css/effect-fade');
        }
    };

    Alpine.data('swiper', (config = {}) => {
        let slider;
        return {
            hasSlides: false,
            isBeginning: false,
            isEnd: false,
            async init() {
                await includeSwiperLibrary();

                this.$nextTick(() => {
                    slider = new Swiper(this.$el.querySelector('.swiper'), {
                        modules: SwiperModules,
                        on: {
                            init: this.updateNavigation.bind(this),
                        },
                        ...config
                    });
                    slider.on('slidesUpdated', this.checkSlides.bind(this));
                    slider.on('slideChange', this.updateNavigation.bind(this));
                });
            },
            slider() {
                return slider;
            },
            destroy() {
                // Destroy the Swiper instance
                slider.destroy();

                // Send it to the garbage collector
                slider = undefined;
            },
            checkSlides(__Swiper) {
                // Determine if there are multiple slides
                this.hasSlides = __Swiper.slides.length > __Swiper.params.slidesPerView;

                // Check if the slider is at the beginning or end
                this.updateNavigation(__Swiper);
            },
            updateNavigation(__Swiper) {
                // Check if the slider is at the beginning or end
                this.isBeginning = __Swiper.isBeginning;
                this.isEnd = __Swiper.isEnd;
            },
        };
    });

    // Active Automatically intercept links.
    window.FireLine.settings.interceptLinks = true;
});

document.addEventListener('fireStart', () => {
    NProgress.start();
});

document.addEventListener('fireEnd', () => {
    NProgress.done();
    window.scrollTo({ top: 0 });
});

document.addEventListener('fireError', () => {
    NProgress.done();
});

document.addEventListener('fireError', () => {
    window.FireLine.context.replaceHtml(
        `<div>
            <div class="mx-auto w-8/12 sm:w-6/12 md:w-5/12 lg:w-4/12 xl:w-3/12 text-center my-16">
                <h2 style="color: --color-amber-400" class="text-6xl font-semibold mb-4">Oops!</h2>
                <p style="color: --color-primary-300" class="text-xl mb-4">Something Wen\'t Wrong, Please Try Again Later.</p>
                <button x-on:click="$fire.reload()" style="color: var(--color-amber-400)">&larr; Go Back</button>
            </div>
        </div>`
    );
});

window.Alpine = Alpine;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Alpine.plugin(persist);
Alpine.plugin(focus);
Alpine.plugin(fireline);
Alpine.start();