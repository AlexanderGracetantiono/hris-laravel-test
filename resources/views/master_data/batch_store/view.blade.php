@extends('layouts.app')

@section('page_title', 'View Batch Store')

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
            
            <h3 class="card-label">Master Data Batch Store</h3>
        </div>
        <div class="card-toolbar">
            {{-- <a href="{{ route("master_data_batch_store_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Batch Store
            </a> --}}
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Batch Code</th>
                    <th>SKU</th>
                    <th>Packaging Center</th>
                    <th>Batch Status</th>
                    <th data-priority="1" width="10px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row["MBSTR_CODE"] }}</td>
                        <td>{{ $row["MBSTR_MPRVE_SKU"] }}</td>
                        <td>{{ $row["MBSTR_MAPLA_TEXT"] }}</td>
                        <td>
                            @if ($row["MBSTR_ACTIVATION_STATUS"] == 1)
                                Waiting
                            @elseif ($row["MBSTR_ACTIVATION_STATUS"] == 2)
                                Ready Sale
                            @elseif ($row["MBSTR_ACTIVATION_STATUS"] == 3)
                                Out Of Stock
                            @endif
                        </td>
                        <td nowrap="nowrap">
                            @if($row["MBSTR_ACTIVATION_STATUS"] == 1 && $row["MBSTR_STORE_ADMIN_CODE"] == session("user_code"))
                                <a href="{{ route('master_data_batch_store_edit', ['code'=> $row['MBSTR_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Batch Store"><i class="la la-edit"></i></a>
                            @else
                                <a href="{{ route('master_data_batch_store_detail',['code'=> $row['MBSTR_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Detail Batch Store"><i class="la la-eye"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
