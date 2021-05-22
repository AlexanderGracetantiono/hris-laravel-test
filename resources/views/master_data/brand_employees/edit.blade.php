@extends('layouts.app')

@section('page_title', 'Edit User Form')

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
                <h3 class="card-title">Edit User Form, Brand : <?php echo $brand["MBRAN_NAME"] ?></h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_employee_brand_update") }}" data-form-success-redirect="{{ route('master_data_employee_brand_view', ['code' => $brand['MBRAN_CODE']]) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="<?php echo $brand["MBRAN_CODE"]; ?>" name="MAEMP_MBRAN_CODE">
                <input type="hidden" value="<?php echo $employee_data["MAEMP_ID"]; ?>" name="employee_id">
                <div class="card-body">
                    <h4>1. User Data</h4><br>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>Brand Name:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $brand["MBRAN_NAME"]; ?>">
                        </div>
                        <div class="col-md-6">
                            <label>User Name:</label>
                            <input disabled type="text" maxlength="255" class="form-control" value="<?php echo $employee_data["MAEMP_TEXT"]; ?>" placeholder="Input user name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>Role:</label>
                            <select class="form-control select2" id="role_select2" name="role_select2">
                                <option></option>
                                <option <?php if ($employee_data["MAEMP_ROLE"] == 3) { echo("selected"); } ?> value="3">PIC Brand</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Phone Number:</label>
                            <div class="input-group">
                                <div class="input-group-prepend mr-3">
                                    <select class="form-control select2 country_code" name="employee_phone_code">
                                        <?php for ($i = 0; $i < count($code); $i++) { ?>
                                            <option <?php if ($employee_data["MAEMP_MACOP_CODE"] == $code[$i]["MACOP_CODE"]) { echo("selected"); } ?> value="<?php echo $code[$i]["MACOP_CODE"] ?>">
                                                <?php echo $code[$i]["MACOP_CODE"] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                &nbsp;
                                <input type="text" maxlength="15"  class="form-control phone_number numeric_input" value="<?php echo $employee_data["MAEMP_PHONE_NUMBER"]; ?>" name="employee_phone" placeholder="Input phone number">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Status:</label>
                            <div class="row">
                                <div class="col-9 col-form-label">
                                    <div class="radio-inline">
                                        <label class="radio radio-primary">
                                            <input type="radio" value="1" name="employee_status_is_active" @if ($employee_data["MAEMP_STATUS"]==1) checked="checked" @endif>
                                            <span></span>Active</label>
                                        <label class="radio radio-primary">
                                            <input type="radio" value="0" name="employee_status_is_active" @if ($employee_data["MAEMP_STATUS"]==0) checked="checked" @endif>
                                            <span></span>Inactive</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><h4>2. Login Account Name</h4><br>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>Account Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="employee_username" value="<?php echo $employee_data["MAEMP_USER_NAME"]; ?>" placeholder="Input account name">
                        </div>
                        <div class="col-md-6">
                            <label>Email:</label>
                            <input readonly type="text" maxlength="255" class="form-control" value="<?php echo $employee_data["MAEMP_EMAIL"]; ?>" name="employee_email" placeholder="Input email">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route('master_data_employee_brand_view', ['code' => $brand['MBRAN_CODE']]) }}" class="btn btn-secondary">Back</a>
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
