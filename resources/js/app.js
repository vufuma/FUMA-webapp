import './bootstrap.js';
// Setup some common globals
window.$ = $;
window.jQuery = jQuery;
window.bootstrap = bootstrap;

import 'tree-multiselect';

import 'datatables.net-bs5';
import 'datatables.net-buttons';
import 'datatables.net-buttons-bs5';
import 'datatables.net-select';
import * as d3 from 'd3';
window.d3 = d3;
import 'd3-queue';
import 'js-loading-overlay'


// Centralize the CSRF token for all AJAX requests
jQuery.ajaxSetup({
    headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
});


// Page state initialize functions - make available globally.
// This side effect also prevent tree shaking
import { 
    setS2GPageState, 
    setG2FPageState, 
    setCelltypePageState, 
    setAnnotPlotPageState, 
    setBrowsePageState} from "./pages/pageStateComponents.js";

window.setS2GPageState = setS2GPageState;
window.setG2FPageState = setG2FPageState;
window.setCelltypePageState = setCelltypePageState;
window.setAnnotPlotPageState = setAnnotPlotPageState;
window.setBrowsePageState = setBrowsePageState;



// Enable all popovers on the current page
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
popoverTriggerList.map(function (popoverTriggerEl) {
    new bootstrap.Popover(popoverTriggerEl);
    popoverTriggerEl.setAttribute('tabindex', 0); // tabindex popover in the default order.
})

import { FumaSetup } from "./utils/fuma.js";
$(function(){
    FumaSetup();
});
//console.log('Completed app side effects');


