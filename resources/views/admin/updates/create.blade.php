@extends('layouts.master')

@section('stylesheets')
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
@endsection

@section('content')
    @include('admin.updates.partials.updateEdit')
@endsection

@push('vite')
    @vite([
        'resources/js/utils/updates.js'])
@endpush

@push('page_scripts')
    <script type="module">
        import { UpdatePageSetup } from "{{ Vite::appjs('utils/updates.js') }}";
        $(function() {
            UpdatePageSetup();
        });
    </script>

@endpush
