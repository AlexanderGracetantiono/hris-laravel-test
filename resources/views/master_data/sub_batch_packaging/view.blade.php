@extends('layouts.app')

@section('page_title', 'View Batch Production')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/sub_batch_packaging/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/sub_batch_packaging/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/sub_batch_packaging/activation_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            
            <h3 class="card-label">Master Data Batch Packaging</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route("master_data_sub_batch_packaging_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Batch
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Batch Code</th>
                    <th>Batch Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th data-priority="1">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr>
                    <td>{{ $row["SUBPA_CODE"] }}</td>
                    <td>{{ $row["SUBPA_TEXT"] }}</td>
                    <td>{{ $row["SUBPA_QTY"] }}</td>
                    <td>
                        @if ($row["SUBPA_ACTIVATION_STATUS"] == 0)
                            Waiting
                        @elseif ($row["SUBPA_ACTIVATION_STATUS"] == 1)
                            In Progress
                        @else
                            Closed
                        @endif
                    </td>
                    <td nowrap="nowrap">
                        @if($row["SUBPA_ACTIVATION_STATUS"] == 0)
                            <a href="{{ route('master_data_sub_batch_packaging_edit', ['code'=> $row['SUBPA_CODE']]) }}" class="btn btn-sm btn-clean btn-icon" title="Edit Sub Batch Packaging"> <i class="la la-edit"></i></a>
                        @endif
                        @if($row["paired_qr"] == 0 && $row["SUBPA_ACTIVATION_STATUS"] != 2 && $row["SUBPA_ACTIVATION_STATUS"] != 3)
                            <button type="button" data-code="{{ $row['SUBPA_CODE'] }}" data-action="{{ route('master_data_sub_batch_packaging_delete') }}"
                                class="btn btn-sm btn-clean btn-icon delete_btn"   title="Delete Sub Batch Packaging"> <i class="la la-trash"></i>
                            </button>
                        @endif
                        @if($row["SUBPA_ACTIVATION_STATUS"] == 1)
                            <!-- <a href="{{ route('master_data_sub_batch_packaging_edit_progress', ['code'=> $row['SUBPA_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"  title="Edit Sub Batch Packaging"> <i class="la la-edit"></i></a> -->
                            <a href="{{ route('master_data_sub_batch_packaging_scanned_qr',['code'=> $row['SUBPA_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Scanned QR Sub Batch Packaging"><i class="la la-qrcode"></i></a>
                        @endif
                        @if($row["SUBPA_ACTIVATION_STATUS"] == 2 || $row["SUBPA_ACTIVATION_STATUS"] == 3)
                            <a href="{{ route('master_data_sub_batch_packaging_scanned_qr_closed',['code'=> $row['SUBPA_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Scanned QR Sub Batch Packaging"><i class="la la-qrcode"></i></a>
                            <a target="_blank" href="{{ route('master_data_sub_batch_packaging_delivery_note', ['code'=> $row['SUBPA_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Download Delivery Note"> <i
                                class="la la-file-alt"></i>
                            </a>
                        @endif
                            <!-- <a href="{{ route('master_data_sub_batch_packaging_detail', ['code'=> $row['SUBPA_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Detail Sub Batch Packaging"> <i class="la la-eye"></i></a> -->
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection