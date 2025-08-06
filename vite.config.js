import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    publicDir: 'resources/public',
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
    },
    plugins: [
        laravel({
            // input: ['resources/css/app.css', 'resources/js/app.js'],
            // input: ['resources/js/pwa.js'],
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pwa.js',
            ],
            refresh: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            // includeAssets: ['favicon.ico', 'apple-touch-icon.png', 'icons/*'],
            includeAssets: [
                'favicon.ico',
                'apple-touch-icon.png',
                'icons/budget.png',
                'icons/report.png'
            ],
            manifest: {
                name: 'SISNEY App',
                short_name: 'SISNEY',
                description: 'Sistem Informasi Keuangan',
                theme_color: '#0d6efd',
                background_color: '#ffffff',
                display: 'standalone',
                start_url: '/',
                scope: '/',
                icons: [
                    {
                        src: 'icons/budget.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: 'icons/report.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                    {
                        src: 'icons/report.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                ],
            },
            workbox: {
                navigateFallback: '/', // ⬅️ fallback untuk index.html
                globPatterns: ['**/*.{js,css,html,png,svg,ico,json}'],
            },
            devOptions: {
                enabled: true, // penting agar bisa testing di dev mode
            },
            server: {
                host: true, // agar bisa diakses dari HP
            }
        }),
    ],
});
