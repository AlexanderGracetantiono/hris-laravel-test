@extends('layouts.app')

@section('page_title', 'View Patient')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product_version/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/product_version/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            
            <h3 class="card-label">Master Data Patient</h3>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Patient Code</th>
                    <th>Test Lab Type</th>
                    <th>Gender</th>
                    <th>Day Of Birth</th>
                    <th>Patient Name</th>
                    <th>NIK</th>
                    <th class="none">Result</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr <?php if ($row['MPRVE_STATUS'] != 1) { echo ("style='background-color:#eeeeee'");} ?>>
                        <td>{{ $row["MPRVE_CODE"] }}</td>
                        <td>{{ $row["MPRVE_MPRCA_TEXT"] }}</td>
                        <td>{{ $row["MPRVE_MPRDT_TEXT"] }}</td>
                        <td>{{ $row["MPRVE_MPRMO_TEXT"] }}</td>
                        <td>{{ $row["MPRVE_TEXT"] }}</td>
                        <td>{{ $row["MPRVE_SKU"] }}</td>
                        <td>{{ $row["MPRVE_NOTES"] }}</td>
                        <td>@if ($row["MPRVE_STATUS"] == 1)
                                Active
                            @else
                                Inactive
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
