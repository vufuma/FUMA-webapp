@extends('layouts.master')

@section('stylesheets')
@endsection

@section('content')
<div id="wrapper" class="active">
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav" id="sidebar-menu">
            <li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
        </ul>
        <ul class="sidebar-nav" id="sidebar">
            <li class="active"><a href="#overview">Wiki Overview<span class="sub_icon fa fa-circle-info"></span></a></li>
            <li><a href="#snp2gene">SNP2GENE<span class="sub_icon fa fa-circle-info"></span></a></li>
            <li><a href="#flames">FLAMES<span class="sub_icon fa fa-circle-info"></span></a></li>
            <li><a href="#xqtls">QTLs Analysis<span class="sub_icon fa fa-circle-info"></span></a></li>
            <li><a href="#faqs">FAQs<span class="sub_icon fa fa-circle-info"></span></a></li>
            <li><a href="#links">Links<span class="sub_icon fa fa-circle-info"></span></a></li>
        </ul>
    </div>

    <div id="page-content-wrapper">
        <div class="page-content inset">
            <div id="overview" class="sidePanel container" style="padding-top:50px; min-height:80vh;">
                <div style="text-align: center;">
                    <h1>Wiki</h1>
                    <h3>Resources on running FUMA modules and interpreting results.</h3>
                </div>
                <div>
                    Documentation and tutorial for FUMA v2.0 and above will be hosted on: <a target="_blank" href="https://fuma-docs.readthedocs.io/en/latest/"> link to documentation</a>
                </div>
            </div>
            <div id="snp2gene" class="sidePanel container" style="padding-top:50px; display: none; min-height:80vh;">
                <h2>SNP2GENE</h2>
                <div style="margin-left: 40px;">
                    <h4>Resources on running SNP2GENE on FUMA</h4>
                    <h5>What is new in SNP2GENE in FUMA v2.0.0?</h5>
                    <video width="600" controls>
                    <source src="{{ asset('storage/whatisnewinsnp2gene_fumav2.mp4') }}" type="video/mp4">
                    Your browser does not support HTML video.
                    </video>
                </div>
            </div>
            <div id="flames" class="sidePanel container" style="padding-top:50px; display: none; min-height:80vh;">
                <h2>FLAMES</h2>
                <div style="margin-left: 40px;">
                    <h4>Resources on running FLAMES on FUMA</h4>
                    <h5>Introduction to the FLAMES module</h5>
                    <video width="600" controls>
                    <source src="{{ asset('storage/flames_intro.mp4') }}" type="video/mp4">
                    Your browser does not support HTML video.
                    </video>
                </div>
            </div>
            <div id="xqtls" class="sidePanel container" style="padding-top:50px; display: none; min-height:80vh;">
                <h2>QTLs Analysis</h2>
                <div style="margin-left: 40px;">
                    <h4>Resources on running QTLs Analysis on FUMA</h4>
                    <h5>Introduction to the QTLs Analysis module</h5>
                    <video width="600" controls>
                    <source src="{{ asset('storage/qtls_analysis_intro.mp4') }}" type="video/mp4">
                    Your browser does not support HTML video.
                    </video>
                </div>
            </div>

            <div id="faqs" class="sidePanel container" style="padding-top:50px; display: none; min-height:80vh;">
                <h2>FAQs</h2>
                @include('wiki._faqs')
            </div>

            <div id="links" class="sidePanel container" style="padding-top:50px; display: none; min-height:80vh;">
                <h2>Links</h2>
                @include('wiki._links')
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