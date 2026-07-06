import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                    'resources/js/app.js',
                    'resources/js/dashboard.js',
                    'resources/js/history-modal.js',
                    'resources/js/monitor-refresh.js',
                    'resources/js/offices.js',
                    'resources/js/queue-refresh.js',
                    'resources/js/register-form.js',
                    'resources/js/skipped.js',
                    'resources/js/statistics.js',
                    'resources/js/transfer-modal.js'],
            refresh: true,
        }),
    ],
});
