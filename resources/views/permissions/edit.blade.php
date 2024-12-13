@extends('layouts.master')
@section('title', '| Edit Permission')

@section('stylesheets')
@endsection

@section('content')
    <div id="page-content-wrapper">
        <div class='col-lg-12 offset-lg-2' style="padding-top:50px;">

            <h1><i class='fa fa-key'></i> Edit {{ $permission->name }}</h1>
            <br>
            {{ html()->modelForm($permission, 'PUT', route('permissions.update', $permission->id))->open() }}

            <div class="form-group @error('name') has-error @enderror">
                {{ html()->label('Permission name')->for('name') }}
                {{ html()->text('name')->placeholder('Permission name')->class(['form-control']) }}
                @error('name')
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @enderror
            </div>
            <br>
            {{ html()->submit('Apply', ['class' => 'btn btn-primary']) }}

            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection

@section('scripts')
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    {{-- Imports from the project --}}
@endsection
