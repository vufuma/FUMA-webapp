// Just doing all imports in app (could move to bootstrap)
console.log("In app.js");
import $ from 'jquery';
window.$ = window.jQuery = $;
// Need this global for tree-multiselect
window.jQuery.fn = $.fn;
import 'tree-multiselect';
//import 'jquery-migrate'
//import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
//import 'bootstrap-select/dist/css/bootstrap-select.min.css';
// because of asynchronous bootstrap load

$.fn.selectpicker.Constructor.BootstrapVersion = '4';
import 'bootstrap-select';
import 'jszip';
import 'pdfmake';
import 'datatables.net-bs4';
import 'datatables.net-buttons';
import 'datatables.net-select';
import 'gasparesganga-jquery-loading-overlay';
import * as d3 from 'd3';
window.d3 = d3;
import 'd3-tip';
import 'd3-queue';

$.ajaxSetup({
    headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
});
window.subdir = "";
window.page = "{{ $page }}";

