// Global shims that are needed in dev mode
// In build mode these are injected by the rollup-inject
// plugin
import $ from 'jquery';
import * as bootstrap from 'bootstrap';

if (import.meta.env.DEV) {
    window.$ = $;
    window.jQuery = $;
    window.bootstrap = bootstrap;
    console.log('shims setup complete in dev mode');
}

export default $;