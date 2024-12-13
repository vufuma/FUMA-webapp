import { defineConfig } from 'vite';
import Inspect from 'vite-plugin-inspect';
import laravel from 'laravel-vite-plugin';
import { nodePolyfills } from 'vite-plugin-node-polyfills';
//import inject from "@rollup/plugin-inject";
import commonjs from '@rollup/plugin-commonjs';
import { nodeResolve } from '@rollup/plugin-node-resolve';
import inject from "@rollup/plugin-inject";
import { visualizer } from 'rollup-plugin-visualizer';


export default defineConfig({
    logLevel: 'info',
    plugins: [
        visualizer({ open: true, filename: 'bundle-visualization.html' }),
        inject({
            $: "jquery",
            jQuery: "jquery",
            bootstrap: ['bootstrap', '*']
        }),
        commonjs({
            //include: "node_modules/**/*.js",
            sourcemap: true,
            requireReturnsDefault: "auto",
            transformMixedEsModules: true,
        }),
        // remove the source map comment for jquery - cause 404 in devtools
        nodePolyfills(),
        Inspect(),
        // The following files can be included in the @vite directive
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pages/page_s2g.js',
                'resources/js/pages/pageStateComponents',
                'resources/js/utils/snp2gene.js',
                'resources/js/utils/updates.js',
                'resources/js/utils/annotPlot.js',
                'resources/js/utils/NewJobParameters.js',
                'resources/js/utils/snp2gene.js',
                'resources/js/utils/browse.js',
                'resources/js/utils/fuma.js',
                'resources/js/utils/celltype.js',
                'resources/js/utils/sidebar.js',
                'resources/js/utils/geneMapParameters.js',
                'resources/js/utils/s2g_results.js',
                'resources/js/utils/g2f_results.js',
                'resources/js/utils/cell_results.js',
                'resources/js/utils/gene2func.js',
                'resources/js/utils/tutorial_utils.js',
            ],
            refresh: true,
        }),


    ],
    resolve: {
        alias: {
            '$': 'jquery',
            'jQuery': 'jquery',
            'Popper': 'popper.js',
        },
    },
    // For running the development server inside the workspace docker
    // The port needs to be exposed in the compose file
    server: {
        host: true,
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
        },
        cors: true
    },
    build: {
        sourcemap: true,
        write: true,
        rollupOptions: {
            treeshake: true,
            preserveEntrySignatures: 'strict', // While minimizing keep export names
            plugins: [
                nodeResolve({
                    browser: true,
                }),
            ],
            output: {
                exports: 'auto',
                generatedCode: {
                    defaultExportMode: 'default', // Retain proper ES module default export
                    symbols: false,
                },
                // Output modern ES Modules
                format: 'esm',
                // d3 and dataTables represent two
                // large logical chunks in the final
                // bundle. Split them out for efficiency. 
                manualChunks(id) {
                    if (id.includes('d3')) {
                        return 'd3';
                    } else if (id.includes('dataTables')) {
                        return 'dataTables';
                    }
                },
                globals: {
                    jquery: 'window.$'
                }
            },
        },
        manifest: true
    },
    css: {
        devSourcemap: true,
    },
});
