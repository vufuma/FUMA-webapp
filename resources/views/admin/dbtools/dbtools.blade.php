@extends('layouts.master')

@section('stylesheets')
@endsection

@section('content')
    <div class="container" style="padding-top: 50px;">
        <div class="table-title">
            <h2>Database <b>Tools</b></h2>
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

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <br>
        <ul>
            <li><a href="{{ url('admin/db-tools/sync-db-storage') }}">Sync Db</a></li>

        </ul>
    </div>
@endsection

@section('scripts')
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    {{-- Imports from the project --}}
@endsection
