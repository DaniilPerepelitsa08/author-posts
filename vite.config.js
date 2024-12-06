import { defineConfig } from 'vite';
import path from 'path';
import laravel from 'laravel-vite-plugin';

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
            ],
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
});
