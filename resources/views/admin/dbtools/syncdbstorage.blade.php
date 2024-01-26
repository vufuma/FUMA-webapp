@extends('layouts.master')

@section('stylesheets')
@endsection

@section('content')
    <div class="container" style="padding-top: 50px;">
        <div class="table-title">
            <h2>Database <b>Tools</b></h2>
            <h4>Sync Db Storage</h4>
        </div>
        <br>
        <p>Syncing the database and storage is a very important task. This is because the database contains the information about the files in the storage. If the database is not in sync with the storage, then the files in the storage will not be accessible. This is a very important task to do when you are moving the project from one server to another.</p>
    </div>
@endsection

@section('scripts')
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    {{-- Imports from the project --}}
@endsection