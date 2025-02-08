import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const isDevelopment = mode === 'development';

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
        ...(isDevelopment && {
            server: {
                host: '192.168.100.9', // Your local machine's IP address
                port: 5173, // Default Vite port
                strictPort: true,
                hmr: {
                    host: '192.168.100.9', // Your local machine's IP address
                },
            },
        }),
    };
});
