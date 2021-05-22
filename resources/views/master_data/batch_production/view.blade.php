@extends('layouts.app')

@section('page_title', 'View Batch Production')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_production/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/activation_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            
            <h3 class="card-label">Master Data Batch Production</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route("master_data_batch_production_add") }}" class="btn btn-primary font-weight-bolder">
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
                    <th width = 20px>Targeted Quantity</th>
                    <th>Production Center</th>
                    <th class="none">Category</th>
                    <th class="none">Product</th>
                    <th class="none">Model</th>
                    <th class="none">Version</th>
                    <th class="none">SKU</th>
                    <th class="none">Product Description</th>
                    <th>Status</th>
                    <th data-priority="1">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr>
                    <td>{{ $row["MABPR_CODE"] }}</td>
                    <td>{{ $row["MABPR_TEXT"] }}</td>
                    <td>{{ $row["MABPR_EXPECTED_QTY"] }}</td>
                    <td>{{ $row["MABPR_MAPLA_TEXT"] }}</td>
                    <td>{{ $row["MABPR_MPRCA_TEXT"] }}</td>
                    <td>{{ $row["MABPR_MPRDT_TEXT"] }}</td>
                    <td>{{ $row["MABPR_MPRMO_TEXT"] }}</td>
                    <td>{{ $row["MABPR_MPRVE_TEXT"] }}</td>
                    <td>{{ $row["MABPR_MPRVE_SKU"] }}</td>
                    <td>{{ $row["MABPR_MPRVE_NOTES"] }}</td>
                    <td>
                        @if ($row["MABPR_ACTIVATION_STATUS"] == 0)
                            Waiting
                        @elseif ($row["MABPR_ACTIVATION_STATUS"] == 1)
                            In Progress
                        @elseif ($row["MABPR_ACTIVATION_STATUS"] == 2)
                            Closed
                        @elseif ($row["MABPR_ACTIVATION_STATUS"] == 3)
                            Accepted
                        @endif
                    </td>
                    <td nowrap="nowrap">
                        @if($row["MABPR_ACTIVATION_STATUS"] == 0)
                            <!-- <a href="{{ route('master_data_batch_production_detail', ['code'=> $row['MABPR_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Detail Batch Production"> <i class="la la-eye"></i></a> -->
                            <a href="{{ route('master_data_batch_production_edit', ['code'=> $row['MABPR_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Batch Production"> <i class="la la-edit"></i></a>
                        @endif
                        @if($row["paired_qr"] == 0 && $row["MABPR_ACTIVATION_STATUS"] != 2 && $row["MABPR_ACTIVATION_STATUS"] != 3)
                            <button type="button" data-code="{{ $row['MABPR_CODE'] }}" data-action="{{ route('master_data_batch_production_delete') }}"
                                class="btn btn-sm btn-clean btn-icon delete_btn"   title="Delete Batch Production"> <i class="la la-trash"></i>
                            </button>
                        @endif
                        @if($row["MABPR_ACTIVATION_STATUS"] == 1)
                            <a href="{{ route('master_data_batch_production_scanned_qr', ['code'=> $row['MABPR_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Scanned QR Batch Production"> <i class="la la-qrcode"></i></a>
                            <!-- <a href="{{ route('master_data_batch_production_detail_in_progress', ['code'=> $row['MABPR_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Detail Batch Production"> <i class="la la-eye"></i></a> -->
                        @endif
                        @if($row["MABPR_ACTIVATION_STATUS"] == 2 || $row["MABPR_ACTIVATION_STATUS"] == 3)
                            <a href="{{ route('master_data_batch_production_scanned_qr_closed', ['code'=> $row['MABPR_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Scanned QR Batch Production"> <i class="la la-qrcode"></i></a>
                            <a target="_blank" href="{{ route('master_data_batch_production_delivery_note', ['code'=> $row['MABPR_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Download Delivery Note"> <i
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
