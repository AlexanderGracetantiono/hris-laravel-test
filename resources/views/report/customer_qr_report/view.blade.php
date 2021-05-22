@extends('layouts.app')

@section('page_title', 'View Customer Report QR')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/report/customer_report_qr/datatable.js?=1') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <h3 class="card-label">Customer Report QR</h3>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Customer Phone Number</th>
                    <th>Customer Notes</th>
                    <th data-priority="1" width="10px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row["REPQR_CST_SCAN_TEXT"] }}</td>
                        <td>{{ $row["REPQR_CST_SCAN_EMAIL"] }}</td>
                        <td>{{ $row["REPQR_CST_SCAN_PHONE_NUMBER"] }}</td>
                        <td>{{ $row["REPQR_CST_SCAN_NOTES"] }}</td>
                        <td nowrap="nowrap">
                            <a href="{{ route('customer_report_qr_detail', ['code'=> $row['REPQR_ID']]) }}" class="btn btn-sm btn-clean btn-icon" title="Detail Customer Report"><i class="la la-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
