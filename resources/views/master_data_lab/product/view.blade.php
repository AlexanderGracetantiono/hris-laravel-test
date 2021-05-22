@extends('layouts.app')

@section('page_title', 'View Gender')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/product/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            
            <h3 class="card-label">Master Data Gender</h3>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Gender Code</th>
                    <th>Test Lab Type</th>
                    <th>Gender Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr <?php if ($row['MPRDT_STATUS'] != 1) { echo ("style='background-color:#eeeeee'");} ?>>
                    <td>{{ $row["MPRDT_CODE"] }}</td>
                    <td>{{ $row["MPRDT_MPRCA_TEXT"] }}</td>
                    <td>{{ $row["MPRDT_TEXT"] }}</td>
                    <td>@if ($row["MPRDT_STATUS"] == 1)
                        Active
                    @else
                        Inactive
                    @endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
