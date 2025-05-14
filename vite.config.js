import { defineConfig } from 'vite';
import Inspect from 'vite-plugin-inspect';
import laravel from 'laravel-vite-plugin';
import { nodePolyfills } from 'vite-plugin-node-polyfills';
import commonjs from '@rollup/plugin-commonjs';
import { nodeResolve } from '@rollup/plugin-node-resolve';
import inject from "@rollup/plugin-inject";
import { visualizer } from 'rollup-plugin-visualizer';


export default defineConfig({
    logLevel: 'info',
    plugins: [
        visualizer({ open: true, filename: 'bundle-visualization.html' }),
        commonjs({
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
                'resources/css/style.css',
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
                'resources/js/utils/jobsSearch.js',
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
                // Ensure that any module using $, jQuery, or bootstrap has it available 
                inject({
                    include: ['**/*.js', '**/*.ts'],
                    exclude: ['**/*.css'],
                    $: "jquery",
                    jQuery: "jquery",
                    bootstrap: ['bootstrap', '*']
                }),
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
                // Output modern ES Modules - no globals needed
                format: 'esm',
                // d3 and jquery/dataTables represent two
                // large logical chunks in the final
                // bundle. Split them out manually for efficiency. 
                manualChunks(id) {
                    if (id.includes('d3')) {
                        return 'd3';
                    } else if (id.includes('dataTables') || id.includes('tree-multiselect')) {
                        return 'dataTables';
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
