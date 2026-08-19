@extends('dashboard.layouts.master')
@section('title', __('backend.venues'))
@push('after-styles')
    <link rel="stylesheet" href="{{ asset('assets/dashboard/js/datatables/datatables.min.css') }}">
@endpush
@section('content')
    <div class="padding">
        <div class="box">
            <div class="box-header dker">
                <div class="row">
                    <div class="col-lg-8 col-sm-6">
                        <h3><i class="material-icons">&#xe0c8;</i> {{ __('backend.venues') }}</h3>
                        <small>
                            <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                            <a>{{ __('backend.venues') }}</a>
                        </small>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered" id="venues-table" style="width:100%">
                    <thead class="dker">
                        <tr>
                            <th style="width:80px">{{ __('backend.id') }}</th>
                            <th>{{ __('backend.name') }}</th>
                            <th>{{ __('backend.city') }}</th>
                            <th style="width:120px">{{ __('backend.capacity') }}</th>
                            <th style="width:80px">{{ __('backend.options') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('after-scripts')
    <script src="{{ asset('assets/dashboard/js/datatables/datatables.min.js') }}"></script>
    <script>
        $(function () {
            $('#venues-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('venues') }}",
                columns: [
                    {data: 0}, {data: 1}, {data: 2}, {data: 3}, {data: 4, orderable: false}
                ],
                order: [[1, 'asc']],
                language: {!! json_encode(__('backend.dataTablesTranslation')) !!}
            });
        });
    </script>
@endpush
