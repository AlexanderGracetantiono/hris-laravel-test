@extends('layouts.app')

@section('page_title', 'View Lab Result')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_store/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_store/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <h3 class="card-label">Master Data Lab Result</h3>
        </div>
        <div class="card-toolbar">
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Chain Code</th>
                    <th>Patient Name</th>
                    <th>Patient Email</th>
                    <th>Patient Phone Number</th>
                    <th data-priority="1" width="10px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row["MASCO_NOTES"] }}</td>
                        <td>{{ $row["SCHED_CUST_NAME"] }}</td>
                        <td>{{ $row["SCHED_CUST_EMAIL"] }}</td>
                        <td>{{ $row["SCHED_CUST_PHONE_NUMBER"] }}</td>
                        <td nowrap="nowrap">
                            @if($row["SCHED_ID"] != null)
                                <a href="{{ route('master_data_lab_batch_store_detail',['code'=> $row['SCHED_ID']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Detail Lab Result"><i class="la la-eye"></i></a>
                                <a target="_blank" href="{{ route('master_data_lab_print_lab_report', ['code'=> $row['SCHED_ID']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Download Lab Result"> <i
                                    class="la la-file-alt"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
