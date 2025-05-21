@extends('layouts.master')
@section('title', '| Users')

@section('stylesheets')
@endsection

@section('content')
    <div id="page-content-wrapper">
        <div class="col-lg-12 offset-lg-2" style="padding-top:50px;">
            <h1><i class="fa fa-users"></i> User Role Administration <a href="{{ route('roles.index') }}"
                    class="btn btn-default pull-right">Roles</a>
                <a href="{{ route('permissions.index') }}" class="btn btn-default pull-right">Permissions</a>
            </h1>
            <hr>
            <p>Type something in the input field to search the table for names, emails or roles:</p>
            <input class="form-control" id="userRoleSearch" type="text" placeholder="Search..">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date/Time Added</th>
                            <th>User Roles</th>
                            <th>Operations</th>
                        </tr>
                    </thead>

                    <tbody id="userRoleTable">
                        @foreach ($users as $user)
                            <tr>

                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('F d, Y h:ia') }}</td>
                                <td>{{ $user->roles()->pluck('name')->implode(' ') }}</td>{{-- Retrieve array of roles associated to a user and convert to string --}}
                                <td>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info pull-left"
                                        style="margin-right: 3px;">Edit</a>

                                    {{ html()->form('DELETE', route('users.destroy', [$user->id]))->open() }}
                                    {{ html()->submit('Delete')->class('btn btn-danger') }}
                                    {{ html()->form()->close() }}


                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <a href="{{ route('users.create') }}" class="btn btn-success" data-bs-toggle="tooltip"
                title="Usually users are added by registration but can be also be added here.">Add User</a>

        </div>
    </div>
@endsection

@push('page_scripts')
    {{-- Imports from the web --}}


    {{-- Hand written ones --}}
    <script type="module">
        var loggedin = "{{ Auth::check() }}";
        var params = {
            sortable: true
        };
        $(function() {
            $("#userRoleSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#userRoleTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
    {{-- Imports from the project --}}
@endpush
