import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/fullcalendar-v6.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],

    server: {
                https: true, // Enable HTTPS
        host: '0.0.0.0',
        //port: 5173,
        hmr: {
            host: 'migrationmanager.bansalcrm.com',
        },

        allowedHosts: [
            'api.migrationmanager.bansalcrm.com',
            'migrationmanager.bansalcrm.com'
        ],
    },

});