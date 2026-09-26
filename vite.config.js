import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // Theme-specific entries so they exist in the Vite manifest
                'resources/css/themes/nimtech.css',
                'resources/js/themes/nimtech.js',
                'resources/css/themes/aspirenoted.css',
                'resources/js/themes/aspirenoted.js',
            ],
            refresh: true,
        }),
    ],
    // Use Vite's default build and optimization settings to avoid
    // over-aggressive tree-shaking or minification that could break JS.
    build: {
        target: 'es2019',
    },
});
