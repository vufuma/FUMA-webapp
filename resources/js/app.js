import './bootstrap.js';
import $ from 'jquery';
window.$ = $;
window.jQuery = $;


import 'bootstrap';
import 'tree-multiselect';
// not used anymore?
///import 'bootstrap-select';
import 'datatables.net-bs5';
import 'datatables.net-buttons';
import 'datatables.net-buttons-bs5';
import 'datatables.net-select';
import * as d3 from 'd3';
window.d3 = d3;
import 'd3-queue';


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
  return new bootstrap.Popover(popoverTriggerEl)
})
//console.log('Completed app side effects');


