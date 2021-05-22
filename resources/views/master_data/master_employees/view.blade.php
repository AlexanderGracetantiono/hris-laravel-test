@extends('layouts.app')

@section('page_title', 'View Employee')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/master_employees/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/master_employees/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/master_employees/reset_password.js') }}"></script>
<script src="{{ asset('custom/js/master_data/master_employees/unblock_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <h3 class="card-label">Master Data User</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route("master_data_employee_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add User
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>User Code</th>
                    <th>User Name</th>
                    <th>Account Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th data-priority="1">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($master_employee_data as $employee)
                <tr <?php if ($employee['MAEMP_STATUS'] != 1) { echo ("style='background-color:#eeeeee'");} ?>>
                    <td>
                        <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">
                            {{ $employee["MAEMP_CODE"] }}
                        </span>
                    </td>
                    <td>
                        <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">
                            {{ $employee["MAEMP_TEXT"] }}
                        </span>
                    </td>
                    <td>
                        <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">
                            {{ $employee["MAEMP_USER_NAME"] }}
                        </span>
                    </td>
                    <td>
                        <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">
                            {{ $employee["MAEMP_EMAIL"] }}
                        </span>
                    </td>
                    <td>
                        <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">
                            {{ $employee["MAEMP_MACOP_CODE"] }}{{ $employee["MAEMP_PHONE_NUMBER"] }}
                        </span>
                    </td>
                    <td>
                        <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">
                            {{ $employee["MAEMP_ROLE"] }}
                        </span>
                    </td>
                    <td>
                        <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">
                            @if ($employee["MAEMP_BLOCKED_STATUS"] == 1)
                                Blocked
                            @elseif ($employee["MAEMP_STATUS"]==1)
                                Active
                            @else
                                Inactive
                            @endif
                        </span>
                    </td>
                    <td nowrap="nowrap">
                        <a href="{{ route("master_data_employee_edit", ["maemp_id" => $employee["MAEMP_ID"]]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Employee"> <i class="la la-edit"></i>
                        </a>
                        @if($employee["MAEMP_ID"] != '1' && $employee["MAEMP_ID"] != '2' && $employee["MAEMP_ID"] != '3')
                            <button type="button" data-code="{{ $employee['MAEMP_ID'] }}" data-action="{{ route('master_data_employee_delete') }}" class="btn btn-sm btn-clean btn-icon delete_btn"   title="Delete Employee"> <i class="la la-trash"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
