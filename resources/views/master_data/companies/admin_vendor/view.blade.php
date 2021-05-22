@extends('layouts.app')

@section('page_title', 'View Admin Vendor')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/admin_vendor/view/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/admin_vendor/view/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            
            <h3 class="card-label">Master Data Admin Vendor</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route("master_data_admin_vendor_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Admin Vendor
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Account Name</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>E-mail</th>
                    <th>Gender</th>
                    <th>Vendor</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maadmin_data as $row)
                <tr>
                    <td>{{ $row["maadmin_username"] }}</td>
                    <td>{{ $row["maadmin_real_name"] }}</td>
                    <td>{{ $row["maadmin_phone"] }}</td>
                    <td>{{ $row["maadmin_email"] }}</td>
                    <td>@if ($row["maadmin_sex"] == 1)
                        Male
                    @else
                        Female
                    @endif</td>
                    <td>{{ $row["mavendor_name"] }}</td>
                    <td>{{ $row["macompany_name"] }}</td>
                    <td>@if ($row["maadmin_status"] == 1)
                        Active
                    @else
                        Inactive
                    @endif</td>
                    <td nowrap="nowrap">
                        <a href="{{ route("master_data_admin_vendor_edit", ["maadmin_username" => $row["maadmin_username"]]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Admin Company"> <i
                            class="la la-edit"></i>
                        </a>
                        <a href="javascript:;" data-maadmin-id="{{ $row["maadmin_id"] }}" data-action="{{ route("master_data_admin_vendor_delete") }}"
                            class="btn btn-sm btn-clean btn-icon delete_btn"   title="Delete Admin Company"> <i class="la la-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
