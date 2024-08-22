import { defineConfig } from 'vite';
import Inspect from 'vite-plugin-inspect';
import laravel from 'laravel-vite-plugin';
import { nodePolyfills } from 'vite-plugin-node-polyfills';

export default defineConfig({
    plugins: [
        nodePolyfills(),
        Inspect(),
        laravel({
            input: [
                'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // For running the development server inside the workspace docker
    // The port needs to be exposed in the compose file
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
            port: 5173,
            clientPort: 5173,
        },
        cors: true
    },
    build: {
        sourcemap: true,
        write: true,
        rollupOptions: {
            // external: [],
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString()
                    }
                },
            },
        },
    },
});
