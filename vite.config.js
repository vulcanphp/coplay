import { defineConfig } from 'vite';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => ({
    plugins: [tailwindcss()],
    base: mode === 'production' ? '/build/' : '/',
    root: path.resolve(__dirname, './resources/app'),
    server: {
        strictPort: true,
        port: 5133,
    },
    build: {
        outDir: path.resolve(__dirname, './public/build'),
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: path.resolve(__dirname, './resources/app/app.js'),
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/app'),
        },
    },
}));