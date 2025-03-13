import { defineConfig } from 'vite';
import path from 'path';

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => ({
    plugins: [],
    base: mode === 'production' ? '/resources/build/' : '/resources/',
    root: path.resolve(__dirname, './public/resources'),
    server: {
        strictPort: true,
        port: 5133,
    },
    build: {
        outDir: path.resolve(__dirname, './public/resources/build'),
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: path.resolve(__dirname, './public/resources/app.js'),
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './public/resources'),
        },
    },
}));