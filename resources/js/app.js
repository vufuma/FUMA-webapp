// Just doing all imports in app (could move to bootstrap)
import 'jquery'
import '../css/app.css'
//import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
//import 'bootstrap-select/dist/css/bootstrap-select.min.css';
// because of asynchronous bootstrap load
import $ from 'jquery';
import * as jQuery from 'jquery';
window.$ = $;
window.jQuery = jQuery;
$.fn.selectpicker.Constructor.BootstrapVersion = '4';
import 'bootstrap-select';
import 'jszip';
import 'pdfmake';
import 'datatables.net';
import 'datatables.net-buttons';
import 'datatables.net-select';
//import 'tree-multiselect';
import * as d3 from 'd3';
window.d3 = d3;
import 'd3-tip';
import 'd3-queue';



