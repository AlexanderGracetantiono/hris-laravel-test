@extends('layouts.app')

@section('page_title', 'Employee Edit Form')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/master_employees/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/master_employees/select2.js') }}"></script>

@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Employee Forgot Account Name Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_employee_account_name_save") }}" data-form-success-redirect="{{ route("master_data_employee_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee_data["MAEMP_ID"] }}">
                <input hidden type="text" maxlength="255" class="form-control" name="employee_code" placeholder="Input employee name" value="{{ $employee_data["MAEMP_CODE"] }}">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>Employee Name:</label>
                            <input disabled type="text" maxlength="255" class="form-control" name="employee_name" placeholder="Input employee name" value="{{ $employee_data["MAEMP_TEXT"] }}">
                        </div>
                        <div class="col-md-6">
                            <label>Account Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="account_name" placeholder="Input account name" value="{{ $employee_data["MAEMP_USER_NAME"] }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_employee_view") }}" class="btn btn-secondary">Back</a>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button id="submit_btn" type="submit" class="btn btn-primary mr-2">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection