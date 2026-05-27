import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/bootstrap.scss',
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/modules/imagePreview.js',
                'resources/js/modules/readMore.js',
                'resources/js/modules/imageViewer.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
