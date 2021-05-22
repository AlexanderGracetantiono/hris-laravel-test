@extends('layouts.app')

@section('page_title', 'View Day Of Birth')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product_model/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/product_model/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <h3 class="card-label">Master Data Day Of Birth</h3>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Day Of Birth Code</th>
                    <th>Test Lab Type</th>
                    <th>Gender</th>
                    <th>Day Of Birth Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr <?php if ($row['MPRMO_STATUS'] != 1) { echo ("style='background-color:#eeeeee'");} ?>>
                        <td>{{ $row["MPRMO_CODE"] }}</td>
                        <td>{{ $row["MPRMO_MPRCA_TEXT"] }}</td>
                        <td>{{ $row["MPRMO_MPRDT_TEXT"] }}</td>
                        <td>{{ $row["MPRMO_TEXT"] }}</td>
                        <td>@if ($row["MPRMO_STATUS"] == 1)
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
