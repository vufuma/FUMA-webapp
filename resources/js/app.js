// This bootstrap is not the the bootstrap5 layout
// it is merely a place to do som common setup
import './bootstrap.js';
import $ from './shims/shims.js'
window.$ = $;

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
    setBrowsePageState,
    setXqtlsPageState} from "./pages/pageStateComponents.js";

window.setS2GPageState = setS2GPageState;
window.setG2FPageState = setG2FPageState;
window.setCelltypePageState = setCelltypePageState;
window.setAnnotPlotPageState = setAnnotPlotPageState;
window.setBrowsePageState = setBrowsePageState;
window.setXqtlsPageState = setXqtlsPageState;



import { FumaSetup, PopoverSetup } from "./utils/fuma.js";
$(function(){
    FumaSetup();
    PopoverSetup();
});

//console.log('Completed app side effects');


