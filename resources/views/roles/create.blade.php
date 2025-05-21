@extends('layouts.master')
@section('title', '| Add Role')

@section('stylesheets')
@endsection

@section('content')
    <div id="page-content-wrapper">
        <div class='col-lg-12 offset-lg-2' style="padding-top:50px;">

            <h1><i class='fa fa-key'></i> Add Role</h1>
            <hr>

            {{ html()->form('POST', route('roles.store'))->open() }}

            <div class="form-group @error('name') has-error @enderror">
                {{ html()->label('Name')->for('name') }}
                {{ html()->text('name')->placeholder('Name')->class(['form-control', 'is-invalid' => $errors->has('name')]) }}
                @error('name')
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @enderror
            </div>

            <h5><b>Assign Permissions</b></h5>

            <div class='form-group @error('permissions') has-error @enderror'>
                @foreach ($permissions as $permission)
                    {{ html()->div(
                        html()->label(
                                html()->checkbox(
                                        'permissions[]',
                                        old('permissions') && in_array($permission->name, old('permissions')) ? true : false,
                                        $permission->id,
                                    )->id('permission-' . $permission->id) .
                                    '&nbsp;' .
                                    ucwords($permission->name),
                            )->for('permission-' . $permission->id) . '<br>',
                    ) }}
                @endforeach
                @error('permissions')
                    <span class="help-block">
                        <strong>{{ $errors->first('permissions') }}</strong>
                    </span>
                @enderror
            </div>

            {{ html()->submit('Add')->class('btn btn-primary') }}
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection

@section('scripts')
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    {{-- Imports from the project --}}
@endsection
