@extends('layouts.master')

@section('stylesheets')
@endsection

@section('content')
    <div class="container" style="padding-top:50px;">
        <div style="text-align: center;">
            <h2>Admin Dashboard</h2>
            <h2>This is Admin's page</h2>
        </div>
        <ul>
            <li><a href="#">Admin</a></li>
            <li><a href="/admin/jobs">Jobs</a></li>
            <li><a href="/admin/search-jobs">Search Jobs</a></li>
            <li><a href="/admin/analysis">Analysis</a></li>
            <li><a href="/admin/updates">Updates</a></li>
            <li><a href="/admin/db-tools">DB tools</a></li>
        </ul>
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
