<?php

$vite_dir = trim(str_replace([root_dir(), DIRECTORY_SEPARATOR], ['', '/'], config('app.vite_dir')), '/');

return [
    [
        'file' => 'vite.config.js',
        'contents' => [
            [
                'text' => <<<EOT
                import { fileURLToPath, URL } from 'node:url'
                import { defineConfig, splitVendorChunkPlugin } from 'vite'
                import liveReload from 'vite-plugin-live-reload'
                import path from 'path'
                
                EOT
            ],
            [
                'feature' => 'vue',
                'text' => <<<EOT
                import vue from '@vitejs/plugin-vue'
                
                EOT
            ],
            [
                'text' => <<<EOT
                
                // https://vitejs.dev/config/
                export default defineConfig({
                    plugins: [
                        liveReload([
                            __dirname + './{$vite_dir}/**/*.php',
                        ]),
                        splitVendorChunkPlugin()
                EOT
            ],
            [
                'feature' => 'vue',
                'text' => <<<EOT
                ,
                        vue()
                EOT
            ],
            [
                'text' => <<<EOT
                    
                    ],
                    base: process.env.NODE_ENV === "production" ? '/{$vite_dir}/dist/' : '/{$vite_dir}/',
                    root: './{$vite_dir}',
                    server: {
                        strictPort: true,
                        port: 5133
                    },
                    build: {
                        outDir: './dist',
                        emptyOutDir: true,
                        manifest: true,
                        rollupOptions: {
                            input: path.resolve(__dirname, './{$vite_dir}/main.js'),
                        }
                    },
                    resolve: {
                        alias: {
                            '@': fileURLToPath(new URL('./{$vite_dir}', import.meta.url))
                        }
                    }
                })
                EOT
            ]
        ]
    ],
    [
        'file' => 'tailwind.config.js',
        'feature' => 'tailwind',
        'contents' => [
            [
                'text' => <<<EOT
                /** @type {import('tailwindcss').Config} */

                module.exports = {
                    content: [
                        "./{$vite_dir}/**/*.{php,html,vue,js,ts,jsx,tsx}",
                    ],
                    theme: {
                        extend: {},
                    },
                    plugins: [],
                }                
                EOT
            ]
        ]
    ],
    [
        'file' => 'README.md',
        'contents' => [
            [
                'text' => <<<EOT
                This template should help get you started developing with Vue 3 in Vite.

                ## Recommended IDE Setup

                [VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur) + [TypeScript Vue Plugin (Volar)](https://marketplace.visualstudio.com/items?itemName=Vue.vscode-typescript-vue-plugin).

                ## Customize configuration

                See [Vite Configuration Reference](https://vitejs.dev/config/).

                ## Project Setup

                ```sh
                npm install
                ```

                ### Compile and Hot-Reload for Development

                ```sh
                npm run dev
                ```

                ### Compile and Minify for Production

                ```sh
                npm run build
                ```
                EOT
            ]
        ]
    ],
    [
        'file' => 'postcss.config.js',
        'feature' => 'postcss',
        'contents' => [
            [
                'text' => <<<EOT
                module.exports = {
                    plugins: {
                        tailwindcss: {}
                EOT
            ],
            [
                'feature' => 'autoprefixer',
                'text' => <<<EOT
                ,
                        autoprefixer: {}
                EOT
            ],
            [
                'text' => <<<EOT
                
                    }
                }
                EOT
            ]
        ]
    ],
    [
        'file' => 'package.json',
        'contents' => [
            [
                'text' => <<<EOT
                {
                    "name": "phpadmin",
                    "version": "1.0.0",
                    "private": true,
                    "scripts": {
                        "dev": "vite",
                        "build": "vite build",
                        "preview": "vite preview"
                    },
                    "dependencies": {
                        
                EOT
            ],
            [
                'feature' => 'vue',
                'text' => <<<EOT
                "vue": "^3.3.4"
                EOT
            ],
            [
                'feature' => ['vue', 'tailwind'],
                'text' => <<<EOT
                ,
                        "axios": "^1.5.1",
                        "vue-router": "^4.2.4",
                        "vuex": "^4.0.2"
                EOT
            ],
            [
                'text' => <<<EOT
                
                    },
                    "devDependencies": {
                        "vite": "^4.4.9",
                        "vite-plugin-live-reload": "^3.0.1"
                EOT
            ],
            [
                'feature' => 'vue',
                'text' => <<<EOT
                ,
                        "@vitejs/plugin-vue": "^4.3.4"
                EOT
            ],
            [
                'feature' => 'tailwind',
                'text' => <<<EOT
                ,
                        "tailwindcss": "^3.3.3"
                EOT
            ],
            [
                'feature' => 'postcss',
                'text' => <<<EOT
                ,
                        "postcss": "^8.4.31"
                EOT
            ],
            [
                'feature' => 'autoprefixer',
                'text' => <<<EOT
                ,
                        "autoprefixer": "^10.4.16"
                EOT
            ],
            [
                'text' => <<<EOT

                    }
                }
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/style.css',
        'contents' => [
            [
                'text' => '',
            ],
            [
                'feature' => 'tailwind',
                'text' => <<<EOT
                @tailwind base;
                @tailwind components;
                @tailwind utilities;
                EOT
            ],
            [
                'not_feature' => 'tailwind',
                'text' => <<<EOT
                :root {
                    font-family: Inter, system-ui, Avenir, Helvetica, Arial, sans-serif;
                    line-height: 1.5;
                    font-weight: 400;
                  
                    color-scheme: light dark;
                    color: rgba(255, 255, 255, 0.87);
                    background-color: #242424;
                  
                    font-synthesis: none;
                    text-rendering: optimizeLegibility;
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                  }
                  
                  a {
                    font-weight: 500;
                    color: #646cff;
                    text-decoration: inherit;
                  }
                  a:hover {
                    color: #535bf2;
                  }
                  
                  body {
                    margin: 0;
                    display: flex;
                    place-items: center;
                    min-width: 320px;
                    min-height: 100vh;
                  }
                  
                  h1 {
                    font-size: 3.2em;
                    line-height: 1.1;
                  }
                  
                  #app {
                    max-width: 1280px;
                    margin: 0 auto;
                    padding: 2rem;
                    text-align: center;
                  }
                  
                  .logo {
                    height: 6em;
                    padding: 1.5em;
                    will-change: filter;
                    transition: filter 300ms;
                  }
                  .logo:hover {
                    filter: drop-shadow(0 0 2em #646cffaa);
                  }
                  .logo.vanilla:hover {
                    filter: drop-shadow(0 0 2em #f7df1eaa);
                  }
                  
                  .card {
                    padding: 2em;
                  }
                  
                  .read-the-docs {
                    color: #888;
                  }
                  
                  button {
                    border-radius: 8px;
                    border: 1px solid transparent;
                    padding: 0.6em 1.2em;
                    font-size: 1em;
                    font-weight: 500;
                    font-family: inherit;
                    background-color: #1a1a1a;
                    cursor: pointer;
                    transition: border-color 0.25s;
                  }
                  button:hover {
                    border-color: #646cff;
                  }
                  button:focus,
                  button:focus-visible {
                    outline: 4px auto -webkit-focus-ring-color;
                  }
                  
                  @media (prefers-color-scheme: light) {
                    :root {
                      color: #213547;
                      background-color: #ffffff;
                    }
                    a:hover {
                      color: #747bff;
                    }
                    button {
                      background-color: #f9f9f9;
                    }
                  }
                  
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/store.js',
        'feature' => ['vue', 'tailwind'],
        'contents' => [
            [
                'text' => <<<EOT
                import { createStore } from 'vuex'
                import axios from './axios'

                const store = createStore({
                    state: {
                        data: false,
                    },
                    actions: {
                        InitData({ commit }, {param}){
                            axios.post('index')
                                .then(({data}) => {
                                    commit('setData', data)
                                })
                        }
                    },
                    mutations: {
                        setData: (state, data) => {
                            state.data = data
                        }
                    },
                    getters: {},
                    modules: {}
                })

                export default store
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/router.js',
        'feature' => ['vue', 'tailwind'],
        'contents' => [
            [
                'text' => <<<EOT
                import { createRouter, createWebHistory } from 'vue-router'

                const router = createRouter({
                    history: createWebHistory(import.meta.env.VITE_BASE_URL),
                    routes: [
                        {
                            path: '/',
                            name: 'home',
                            component: () => import('./views/Home.vue')
                        }
                    ]
                })

                export default router
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/main.js',
        'contents' => [
            [
                'text' => <<<EOT
                import './style.css'
                
                EOT
            ],
            [
                'feature' => ['vue', 'tailwind'],
                'text' => <<<EOT
                import { createApp } from 'vue'
                import app from './App.vue'
                import router from './router'
                import store from './store'

                createApp(app)
                    .use(router)
                    .use(store)
                    .mount('#app')
                EOT
            ],
            [
                'feature' => 'vanilla',
                'not_feature' => 'tailwind',
                'text' => <<<EOT
                import './style.css'
                import { setupCounter } from './counter.js'
                import ViteLogo from './vite.svg'
                import JavascriptLogo from './javascript.svg'

                document.querySelector('#app').innerHTML = `
                <div>
                    <a href="https://vitejs.dev" target="_blank">
                    <img src="\${ViteLogo}" class="logo" alt="Vite logo" />
                    </a>
                    <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript" target="_blank">
                    <img src="\${JavascriptLogo}" class="logo vanilla" alt="JavaScript logo" />
                    </a>
                    <h1>Hello Vite!</h1>
                    <div class="card">
                    <button id="counter" type="button"></button>
                    </div>
                    <p class="read-the-docs">
                    Click on the Vite logo to learn more
                    </p>
                </div>
                `

                setupCounter(document.querySelector('#counter'))
                EOT
            ],
            [
                'feature' => 'vue',
                'not_feature' => 'tailwind',
                'text' => <<<EOT
                import { createApp } from 'vue'
                import App from './App.vue'

                createApp(App).mount('#app')
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/counter.js',
        'feature' => 'vanilla',
        'contents' => [
            [
                'text' => <<<EOT
                export function setupCounter(element) {
                    let counter = 0
                    const setCounter = (count) => {
                      counter = count
                      element.innerHTML = `count is \${counter}`
                    }
                    element.addEventListener('click', () => setCounter(counter + 1))
                    setCounter(0)
                  }
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/javascript.svg',
        'feature' => 'vanilla',
        'contents' => [
            [
                'text' => <<<EOT
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--logos" width="32" height="32" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 256"><path fill="#F7DF1E" d="M0 0h256v256H0V0Z"></path><path d="m67.312 213.932l19.59-11.856c3.78 6.701 7.218 12.371 15.465 12.371c7.905 0 12.89-3.092 12.89-15.12v-81.798h24.057v82.138c0 24.917-14.606 36.259-35.916 36.259c-19.245 0-30.416-9.967-36.087-21.996m85.07-2.576l19.588-11.341c5.157 8.421 11.859 14.607 23.715 14.607c9.969 0 16.325-4.984 16.325-11.858c0-8.248-6.53-11.17-17.528-15.98l-6.013-2.58c-17.357-7.387-28.87-16.667-28.87-36.257c0-18.044 13.747-31.792 35.228-31.792c15.294 0 26.292 5.328 34.196 19.247l-18.732 12.03c-4.125-7.389-8.591-10.31-15.465-10.31c-7.046 0-11.514 4.468-11.514 10.31c0 7.217 4.468 10.14 14.778 14.608l6.014 2.577c20.45 8.765 31.963 17.7 31.963 37.804c0 21.654-17.012 33.51-39.867 33.51c-22.339 0-36.774-10.654-43.819-24.574"></path></svg>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/vite.svg',
        'feature' => 'vanilla',
        'contents' => [
            [
                'text' => <<<EOT
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--logos" width="31.88" height="32" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 257"><defs><linearGradient id="IconifyId1813088fe1fbc01fb466" x1="-.828%" x2="57.636%" y1="7.652%" y2="78.411%"><stop offset="0%" stop-color="#41D1FF"></stop><stop offset="100%" stop-color="#BD34FE"></stop></linearGradient><linearGradient id="IconifyId1813088fe1fbc01fb467" x1="43.376%" x2="50.316%" y1="2.242%" y2="89.03%"><stop offset="0%" stop-color="#FFEA83"></stop><stop offset="8.333%" stop-color="#FFDD35"></stop><stop offset="100%" stop-color="#FFA800"></stop></linearGradient></defs><path fill="url(#IconifyId1813088fe1fbc01fb466)" d="M255.153 37.938L134.897 252.976c-2.483 4.44-8.862 4.466-11.382.048L.875 37.958c-2.746-4.814 1.371-10.646 6.827-9.67l120.385 21.517a6.537 6.537 0 0 0 2.322-.004l117.867-21.483c5.438-.991 9.574 4.796 6.877 9.62Z"></path><path fill="url(#IconifyId1813088fe1fbc01fb467)" d="M185.432.063L96.44 17.501a3.268 3.268 0 0 0-2.634 3.014l-5.474 92.456a3.268 3.268 0 0 0 3.997 3.378l24.777-5.718c2.318-.535 4.413 1.507 3.936 3.838l-7.361 36.047c-.495 2.426 1.782 4.5 4.151 3.78l15.304-4.649c2.372-.72 4.652 1.36 4.15 3.788l-11.698 56.621c-.732 3.542 3.979 5.473 5.943 2.437l1.313-2.028l72.516-144.72c1.215-2.423-.88-5.186-3.54-4.672l-25.505 4.922c-2.396.462-4.435-1.77-3.759-4.114l16.646-57.705c.677-2.35-1.37-4.583-3.769-4.113Z"></path></svg>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/index.php',
        'contents' => [
            [
                'text' => <<<EOT
                <!DOCTYPE html>
                <html lang="en">

                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="icon" type="image/svg+xml" href="https://vitejs.dev/logo.svg">
                    <title>Vite Application Starter Pack</title>
                    <?= vite(['running' => null]) ?>
                </head>

                EOT
            ],
            [
                'feature' => 'tailwind',
                'text' => <<<EOT

                <body class="w-full md:w-10/12 lg:w-8/12 xl:w-6/12 mx-auto bg-slate-900 text-center">
                
                EOT
            ],
            [
                'not_feature' => 'tailwind',
                'text' => <<<EOT
                <body>
                EOT
            ],
            [
                'text' => <<<EOT

                    <div id="app"></div>

                </body>

                </html>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/axios.js',
        'feature' => ['vue', 'tailwind'],
        'contents' => [
            [
                'text' => <<<EOT
                import axios from "axios"

                const axiosClient = axios.create({
                    baseURL: `\${import.meta.env.VITE_API_BASE_URL}`
                })

                axiosClient.interceptors.response.use(response => {
                    return response
                }, error => {
                    if (error.response.status === 404) {
                        // do whatever you want
                    }
                    throw error
                })

                export default axiosClient
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/App.vue',
        'feature' => ['vue', 'tailwind'],
        'contents' => [
            [
                'text' => <<<EOT
                <template>
                    <div class="py-20">
                        <div class="flex justify-center items-center">
                            <a href="https://vitejs.dev" target="_blank">
                                <img :src="viteLogo" class="w-[135px]" alt="Vite logo" />
                            </a>
                            <a href="https://tailwindcss.com" target="_blank" class="mx-10">
                                <img :src="tailwindLogo" class="w-[145px]" alt="Vite logo" />
                            </a>
                            <a href="https://vuejs.org/" target="_blank">
                                <img :src="vueLogo" class="w-[135px]" alt="Vue logo" />
                            </a>
                        </div>
                        <router-view :key="\$route.path"></router-view>
                    </div>
                </template>

                <script setup>
                    import { RouterView } from "vue-router"
                    import viteLogo from "./vite.svg"
                    import vueLogo from "./vue.svg"
                    import tailwindLogo from "./tailwind.svg"
                </script>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/.env',
        'contents' => [
            [
                'text' => <<<EOT
                VITE_BASE_URL = '/'

                EOT
            ],
            [
                'feature' => ['vue', 'tailwind'],
                'text' => <<<EOT
                VITE_API_BASE_URL = http://127.0.0.1:8080/api/
                
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/views/Home.vue',
        'feature' => ['vue', 'tailwind'],
        'contents' => [
            [
                'text' => <<<EOT
                <template>
                    <div class="mt-14">
                        <h1 class="text-5xl font-bold text-gray-300 mb-12">Vite + Tailwind + Vue</h1>
                        <button class="px-5 py-2 bg-purple-600 hover:bg-purple-700 rounded text-white font-semibold text-xl" @click="count++">count is {{ count }}</button>

                        <p class="text-gray-200 mt-6 mb-10">
                            <span>Edit</span>
                            <code class="text-rose-500 mx-2 underline">views/Home.vue</code>
                            <span>to test HMR</span>
                        </p>
                        <p class="text-lg text-gray-100 mb-4">
                            Check out
                            <a href="https://vuejs.org/guide/quick-start.html#local" class="text-purple-400 hover:text-purple-500" target="_blank">create-vue</a>, the official Vue + Vite starter
                        </p>
                        <p class="text-lg text-gray-100">
                            Install
                            <a href="https://github.com/vuejs/language-tools" class="text-purple-400 hover:text-purple-500" target="_blank">Volar</a>
                            in your IDE for a better DX    
                        </p>
                        <p class="text-gray-300 text-sm mt-6">Click on the Vite, Tailwind and Vue logos to learn more</p>
                    </div>
                </template>

                <script setup>
                    import { ref } from 'vue'
                    const count = ref(0)
                </script>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/vite.svg',
        'feature' => ['vue', 'tailwind'],
        'contents' => [
            [
                'text' => <<<EOT
                <svg viewBox="0 0 410 404" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M399.641 59.5246L215.643 388.545C211.844 395.338 202.084 395.378 198.228 388.618L10.5817 59.5563C6.38087 52.1896 12.6802 43.2665 21.0281 44.7586L205.223 77.6824C206.398 77.8924 207.601 77.8904 208.776 77.6763L389.119 44.8058C397.439 43.2894 403.768 52.1434 399.641 59.5246Z" fill="url(#paint0_linear)"/><path d="M292.965 1.5744L156.801 28.2552C154.563 28.6937 152.906 30.5903 152.771 32.8664L144.395 174.33C144.198 177.662 147.258 180.248 150.51 179.498L188.42 170.749C191.967 169.931 195.172 173.055 194.443 176.622L183.18 231.775C182.422 235.487 185.907 238.661 189.532 237.56L212.947 230.446C216.577 229.344 220.065 232.527 219.297 236.242L201.398 322.875C200.278 328.294 207.486 331.249 210.492 326.603L212.5 323.5L323.454 102.072C325.312 98.3645 322.108 94.137 318.036 94.9228L279.014 102.454C275.347 103.161 272.227 99.746 273.262 96.1583L298.731 7.86689C299.767 4.27314 296.636 0.855181 292.965 1.5744Z" fill="url(#paint1_linear)"/><defs><linearGradient id="paint0_linear" x1="6.00017" y1="32.9999" x2="235" y2="344" gradientUnits="userSpaceOnUse"><stop stop-color="#41D1FF"/><stop offset="1" stop-color="#BD34FE"/></linearGradient><linearGradient id="paint1_linear" x1="194.651" y1="8.81818" x2="236.076" y2="292.989" gradientUnits="userSpaceOnUse"><stop stop-color="#FFEA83"/><stop offset="0.0833333" stop-color="#FFDD35"/><stop offset="1" stop-color="#FFA800"/></linearGradient></defs></svg>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/vue.svg',
        'feature' => ['vue', 'tailwind'],
        'contents' => [
            [
                'text' => <<<EOT
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 198"><path fill="#41B883" d="M204.8 0H256L128 220.8L0 0h97.92L128 51.2L157.44 0h47.36Z"></path><path fill="#41B883" d="m0 0l128 220.8L256 0h-51.2L128 132.48L50.56 0H0Z"></path><path fill="#35495E" d="M50.56 0L128 133.12L204.8 0h-47.36L128 51.2L97.92 0H50.56Z"></path></svg>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/tailwind.svg',
        'feature' => ['vue', 'tailwind'],
        'contents' => [
            [
                'text' => <<<EOT
                <svg height="1504" preserveAspectRatio="xMidYMid" width="2500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 153.6"><linearGradient id="a" x1="-2.778%" y1="32%" y2="67.556%"><stop offset="0" stop-color="#2298bd"/><stop offset="1" stop-color="#0ed7b5"/></linearGradient><path d="M128 0C93.867 0 72.533 17.067 64 51.2 76.8 34.133 91.733 27.733 108.8 32c9.737 2.434 16.697 9.499 24.401 17.318C145.751 62.057 160.275 76.8 192 76.8c34.133 0 55.467-17.067 64-51.2-12.8 17.067-27.733 23.467-44.8 19.2-9.737-2.434-16.697-9.499-24.401-17.318C174.249 14.743 159.725 0 128 0zM64 76.8C29.867 76.8 8.533 93.867 0 128c12.8-17.067 27.733-23.467 44.8-19.2 9.737 2.434 16.697 9.499 24.401 17.318C81.751 138.857 96.275 153.6 128 153.6c34.133 0 55.467-17.067 64-51.2-12.8 17.067-27.733 23.467-44.8 19.2-9.737-2.434-16.697-9.499-24.401-17.318C110.249 91.543 95.725 76.8 64 76.8z" fill="url(#a)"/></svg>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/components/Intro.vue',
        'feature' => ['vue', 'tailwind'],
        'contents' => [
            [
                'text' => <<<EOT
                <template>
                    <div class="py-8 lg:py-10">
                        <div v-if="icon">
                            <img :src="icon" class="h-16 w-16 mb-2 rounded-full" alt="author">
                        </div>
                        <h1 class="pt-3 font-body text-2xl font-semibold text-gray-800 md:text-3xl lg:text-4xl" v-html="title"></h1>
                        <p class="pt-3 font-body text-xl font-light text-primary text-gray-800">
                            <slot />
                        </p>
                        <a v-if="button" :href="button.url"
                            :target="button.target ?? '_self'"
                            class="mt-12 block bg-purple-600 px-10 py-4 text-center text-white font-body text-xl font-semibold transition-colors hover:bg-purple-700 sm:inline-block sm:text-left sm:text-2xl">
                            {{ button.text }}
                        </a>
                    </div>
                </template>
                
                <script setup>
                    defineProps(["icon", "title", "button"])
                </script>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/vue.svg',
        'feature' => 'vue',
        'not_feature' => 'tailwind',
        'contents' => [
            [
                'text' => <<<EOT
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--logos" width="37.07" height="36" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 198"><path fill="#41B883" d="M204.8 0H256L128 220.8L0 0h97.92L128 51.2L157.44 0h47.36Z"></path><path fill="#41B883" d="m0 0l128 220.8L256 0h-51.2L128 132.48L50.56 0H0Z"></path><path fill="#35495E" d="M50.56 0L128 133.12L204.8 0h-47.36L128 51.2L97.92 0H50.56Z"></path></svg>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/vite.svg',
        'feature' => 'vue',
        'not_feature' => 'tailwind',
        'contents' => [
            [
                'text' => <<<EOT
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--logos" width="31.88" height="32" preserveAspectRatio="xMidYMid meet" viewBox="0 0 256 257"><defs><linearGradient id="IconifyId1813088fe1fbc01fb466" x1="-.828%" x2="57.636%" y1="7.652%" y2="78.411%"><stop offset="0%" stop-color="#41D1FF"></stop><stop offset="100%" stop-color="#BD34FE"></stop></linearGradient><linearGradient id="IconifyId1813088fe1fbc01fb467" x1="43.376%" x2="50.316%" y1="2.242%" y2="89.03%"><stop offset="0%" stop-color="#FFEA83"></stop><stop offset="8.333%" stop-color="#FFDD35"></stop><stop offset="100%" stop-color="#FFA800"></stop></linearGradient></defs><path fill="url(#IconifyId1813088fe1fbc01fb466)" d="M255.153 37.938L134.897 252.976c-2.483 4.44-8.862 4.466-11.382.048L.875 37.958c-2.746-4.814 1.371-10.646 6.827-9.67l120.385 21.517a6.537 6.537 0 0 0 2.322-.004l117.867-21.483c5.438-.991 9.574 4.796 6.877 9.62Z"></path><path fill="url(#IconifyId1813088fe1fbc01fb467)" d="M185.432.063L96.44 17.501a3.268 3.268 0 0 0-2.634 3.014l-5.474 92.456a3.268 3.268 0 0 0 3.997 3.378l24.777-5.718c2.318-.535 4.413 1.507 3.936 3.838l-7.361 36.047c-.495 2.426 1.782 4.5 4.151 3.78l15.304-4.649c2.372-.72 4.652 1.36 4.15 3.788l-11.698 56.621c-.732 3.542 3.979 5.473 5.943 2.437l1.313-2.028l72.516-144.72c1.215-2.423-.88-5.186-3.54-4.672l-25.505 4.922c-2.396.462-4.435-1.77-3.759-4.114l16.646-57.705c.677-2.35-1.37-4.583-3.769-4.113Z"></path></svg>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/App.vue',
        'feature' => 'vue',
        'not_feature' => 'tailwind',
        'contents' => [
            [
                'text' => <<<EOT
                <script setup>
                import HelloWorld from './components/HelloWorld.vue'
                </script>

                <template>
                <div>
                    <a href="https://vitejs.dev" target="_blank">
                    <img src="./vite.svg" class="logo" alt="Vite logo" />
                    </a>
                    <a href="https://vuejs.org/" target="_blank">
                    <img src="./vue.svg" class="logo vue" alt="Vue logo" />
                    </a>
                </div>
                <HelloWorld msg="Vite + Vue" />
                </template>

                <style scoped>
                    .logo {
                    height: 6em;
                    padding: 1.5em;
                    will-change: filter;
                    transition: filter 300ms;
                    }
                    .logo:hover {
                    filter: drop-shadow(0 0 2em #646cffaa);
                    }
                    .logo.vue:hover {
                    filter: drop-shadow(0 0 2em #42b883aa);
                    }
                </style>
                EOT
            ]
        ]
    ],
    [
        'file' => $vite_dir . '/components/HelloWorld.vue',
        'feature' => 'vue',
        'not_feature' => 'tailwind',
        'contents' => [
            [
                'text' => <<<EOT
                <script setup>
                import { ref } from 'vue'

                defineProps({
                msg: String,
                })

                const count = ref(0)
                </script>

                <template>
                <h1>{{ msg }}</h1>

                <div class="card">
                    <button type="button" @click="count++">count is {{ count }}</button>
                    <p>
                    Edit
                    <code>components/HelloWorld.vue</code> to test HMR
                    </p>
                </div>

                <p>
                    Check out
                    <a href="https://vuejs.org/guide/quick-start.html#local" target="_blank"
                    >create-vue</a
                    >, the official Vue + Vite starter
                </p>
                <p>
                    Install
                    <a href="https://github.com/vuejs/language-tools" target="_blank">Volar</a>
                    in your IDE for a better DX
                </p>
                <p class="read-the-docs">Click on the Vite and Vue logos to learn more</p>
                </template>

                <style scoped>
                    .read-the-docs {
                        color: #888;
                    }
                </style>
                EOT
            ]
        ]
    ]
];
