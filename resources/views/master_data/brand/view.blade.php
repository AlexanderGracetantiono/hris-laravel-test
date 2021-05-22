@extends('layouts.app')

@section('page_title', 'View Brand')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/brand/view/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/brand/view/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <h3 class="card-label">Master Data Brand</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route("master_data_brand_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Brand
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Brand Code</th>
                    <th>Brand Name</th>
                    <th>Company Name</th>
                    <th>Status</th>
                    <th width="10px" data-priority="1">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr <?php if ($row['MBRAN_STATUS'] != 1) { echo ("style='background-color:#eeeeee'");} ?>>
                    <td>{{ $row["MBRAN_CODE"] }}</td>
                    <td>{{ $row["MBRAN_NAME"] }}</td>
                    <td>{{ $row["MBRAN_MCOMP_NAME"] }}</td>
                    <td>
                        @if ($row["MBRAN_STATUS"] == 1)
                            Active
                        @else
                            Inactive
                        @endif
                    </td>
                    <td nowrap="nowrap">
                        <a href="{{ route('master_data_employee_brand_view', ['code'=> $row['MBRAN_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Assign User"> <i
                            class="la la-user"></i>
                        </a>
                        <a href="{{ route('master_data_brand_edit', ['code'=> $row['MBRAN_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Brand"> <i
                            class="la la-edit"></i>
                        </a>
                        <!-- <button type="button" data-code="{{ $row['MBRAN_CODE'] }}" data-action="{{ route('master_data_brand_delete') }}"
                            class="btn btn-sm btn-clean btn-icon delete_btn"   title="Delete Brand"> <i class="la la-trash"></i>
                        </button> -->
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
