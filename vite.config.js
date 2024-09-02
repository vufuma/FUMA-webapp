import { defineConfig } from 'vite';
import Inspect from 'vite-plugin-inspect';
import laravel from 'laravel-vite-plugin';
import { nodePolyfills } from 'vite-plugin-node-polyfills';
import inject from "@rollup/plugin-inject";
import cleanup from 'rollup-plugin-cleanup';

export default defineConfig({
    plugins: [
        // adds imports in code were needed import $ from 'jquery'
        inject({
            $: 'jquery',
            jQuery: 'jquery',
        }),
        cleanup({ comments: 'none' }),
        // remove the source map comment for jquery - cause 404 in devtools
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
                'resources/js/s2g_results.js',
                'resources/js/cell_results.js',
                ],
            refresh: true,
        }),
    ],
    resolve: {
        // Aliases that will be used in Laravel Blade templates
        // must be defined as macros in app/Providers/AppServiceProvider.php
        // see doc https://laravel.com/docs/11.x/vite#blade-aliases
        // The standard aliases defined here ill only work in pure javascript or html
        alias: {
            'appjs': './resources/js',
        }
    },
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
