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
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/NewJobParameters.js',
                'resources/js/snp2gene.js',
                'resources/js/fuma.js',
                'resources/js/celltype.js',
                'resources/js/sidebar.js',
                'resources/js/geneMapParameters.js',
                ],
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
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString()
                    }
                },
            },
        },
        manifest: true
    },
    css: {
        devSourcemap: true,
    },
});
