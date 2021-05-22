@extends('layouts.app')

@section('page_title', 'View Brand')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/vendor/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/vendor/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <h3 class="card-label">Master Data Brand</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route("master_data_vendor_add") }}" class="btn btn-primary font-weight-bolder">
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
                    <th>Brand Address</th>
                    <th>Brand Company</th>
                    <th>Status</th>
                    <th data-priority="1">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr>
                    <td>{{ $row["MVNDR_CODE"] }}</td>
                    <td>{{ $row["MVNDR_NAME"] }}</td>
                    <td>{{ $row["MVNDR_ADDRESS"] }}</td>
                    <td>{{ $row["MVNDR_MCOMP_NAME"] }}</td>
                    <td>
                        @if ($row["MVNDR_STATUS"] == 1)
                            Active
                        @else
                            Inactive
                        @endif
                    </td>
                    <td nowrap="nowrap">
                        <a href="{{ route('master_data_vendor_edit', ['code' => $row['MVNDR_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Brand"> <i
                            class="la la-edit"></i>
                        </a>
                        <a href="{{ route('master_data_vendor_edit', ['code' => $row['MVNDR_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="User Brand"> <i
                            class="la la-user"></i>
                        </a>
                        <button type="button" data-code="{{ $row['MVNDR_CODE'] }}" data-action="{{ route('master_data_vendor_delete') }}"
                            class="btn btn-sm btn-clean btn-icon delete_btn"   title="Delete Brand"> <i class="la la-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
