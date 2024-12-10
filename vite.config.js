// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/admin/app.js',
                'resources/admin/dashboard.js',
                'resources/admin/complaint.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@admin': path.resolve(__dirname, 'resources/admin'),
        },
    },
});
