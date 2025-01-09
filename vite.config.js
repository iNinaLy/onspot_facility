// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
                'resources/admin/app.js',
                'resources/admin/dashboard.js',
                'resources/admin/complaint.js',
                'resources/supervisor/app.js',
                'resources/supervisor/dashboard.js',
                'resources/supervisor/complaint.js',
                'resources/supervisor/cleaner.js',
                'resources/supervisor/cleaner.css',
                'resources/supervisor/history.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@admin': path.resolve(__dirname, 'resources/admin'),
            '@supervisor': path.resolve(__dirname, 'resources/supervisor'),
        },
    },
});
