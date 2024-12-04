@extends('layouts.master')

@section('stylesheets')
    @livewireStyles
@endsection

@section('content')
    <div class="container" style="padding-top: 50px;">
        <div class="table-title">
            <h2>Database <b>Tools</b></h2>
            <h4>Sync Db Storage</h4>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <br>
        <p>Syncing the database and storage is a very important task. This is because the database contains the information
            about the files in the storage. If the database is not in sync with the storage, then the files in the storage
            will not be accessible. This is a very important task to do when you are moving the project from one server to
            another. Brief instruction: find a time when there is no queued jobs, bring the server down, run the listDirectoryContents.
            Then, delete the discrepancies using the Del button, which will create a delDirectoryAndDbContents job.</p>

        <livewire:syncdbstorage-jobs />

        {{ html()->form('POST', url('admin/db-tools/sync-db-storage/new_listing_job'))->open() }}
        <div>
            <button type="submit" class="btn btn-info" style="float: right;">Start Listing Jobs</button>
        </div>
        {{ html()->form()->close() }}
    </div>
@endsection

@section('scripts')
    @livewireScripts
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    <script>
        function toggle(source, name) {
            checkboxes = document.getElementsByName(name);
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>

    {{-- Imports from the project --}}
@endsection
