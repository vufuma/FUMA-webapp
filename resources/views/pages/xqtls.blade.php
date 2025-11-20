@extends('layouts.master')

@section('stylesheets')
@endsection

@section('content')
    <div class ="col">
        <div class="container" style="padding-top:50px;">
            <div style="text-align: center;">
                <h3>xQTLs Analysis</h3>
                <p> Compute genetic correlation for a genomic region of interest (for example, a genomic risk loci) with various QTLs datasets. </p>
                <div id="uploadData">
                    <table class="table table-bordered inputTable" id="xqtlsAnalysis" style="width: auto;">
                        <tr>
                            <td> GWAS summary statistics file: </td>
                            <td><input type="file" class="form-control-file" name="GWASsummary" id="GWASsummary" /></td>
                        </tr>
                        <tr>
                            <td> Information about the genomic region of interest: </td>
                            <td>
                                <span class="inputSpan">Chromosome: <input type="text" class="form-control"
                                            id="chrcol" name="chr"></span>
                                <span class="inputSpan">Start: <input type="text" class="form-control"
                                            id="startpos" name="start"></span>
                                <span class="inputSpan">End: <input type="text" class="form-control"
                                            id="endpos" name="end"></span>
                            </td>
                        <tr>
                            <td>Available datasets:</td>
                            <td>
                                <!-- eQTLs GTEx v8 -->
                                <div class="accordion-item" style="padding:0px;">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlGtexv8">
                                            eQTLs GTEx v8
                                        </button>
                                    </h5>
                                    <div class="accordion-collapse collapse" id="eqtlGtexv8">
                                        <div class="accordion-body">
                                            <span class="multiSelect">
                                                <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                                <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                                <select multiple class="form-select" id="eqtlGtexv8Ts" name="eqtlGtexv8Ts[]"
                                                    size="10" onchange="window.CheckAll();">
                                                    @include('snp2gene.xqtls_options.eqtls.eqtls_gtexv8_options')
                                                </select>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- eQTL Catalog -->
                                <div class="accordion-item" style="padding:0px; accordion-bg:gray;">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlCatalog">
                                            eQTLs Catalog
                                        </button>
                                    </h5>
                                    <div class="accordion-collapse collapse" id="eqtlCatalog">
                                        <div class="accordion-body">
                                            <span class="multiSelect">
                                                <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                                <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                                <select multiple class="form-select" id="eqtlCatalogTs" name="eqtlCatalogTs[]"
                                                    size="10" onchange="window.CheckAll();">
                                                    @include('snp2gene.xqtls_options.eqtls.eqtls_eqtlcatalog_options')
                                                </select>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- pQTLs Plasma -->
                                <div class="accordion-item" style="padding:0px;">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#plasma">
                                            pQTLs Plasma
                                        </button>
                                    </h5>
                                    <div class="accordion-collapse collapse" id="plasma">
                                        <div class="accordion-body">
                                            <span class="multiSelect">
                                                <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                                <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                                <select multiple class="form-select" id="pqtlPlasmaDs" name="pqtlPlasmaDs[]"
                                                    size="10" onchange="window.CheckAll();">
                                                    @include('snp2gene.xqtls_options._pqtl_plasma_options')
                                                </select>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>

                    
                    
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('vite')
    @vite([
        'resources/js/utils/sidebar.js',
        'resources/js/utils/browse.js'])
@endpush

@push('page_scripts')

    <script type="module">
        import { SidebarSetup } from "{{ Vite::appjs('utils/sidebar.js') }}";
        import { BrowseSetup } from "{{ Vite::appjs('utils/browse.js') }}";
        $(function(){
            SidebarSetup();
            BrowseSetup();
        })
    </script>

@endpush