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
                <h3 class="card-title">User Edit Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_employee_update") }}" data-form-success-redirect="{{ route("master_data_employee_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee_data["MAEMP_ID"] }}">
                <div class="card-body">
                    <h4>1. User Data</h4><br>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>Brand Name:</label>
                            <input disabled type="text" class="form-control" value="<?php echo session("brand_name"); ?>">
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
                                <?php if (session('user_role') == 1) { ?>
                                    <option <?php if ($employee_data["MAEMP_ROLE"] == 1) { echo("selected"); } ?> value="1">CekOri Administrator</option>
                                <?php } ?>
                                <?php if (session('brand_type') == 1) { ?>
                                    <?php if (session('user_role') == 3) { ?>
                                        <option <?php if ($employee_data["MAEMP_ROLE"] == 4) { echo("selected"); } ?>  value="4">Production Administrator</option>
                                        <option <?php if ($employee_data["MAEMP_ROLE"] == 5) { echo("selected"); } ?>  value="5">Packaging Administrator</option>
                                        <option <?php if ($employee_data["MAEMP_ROLE"] == 8) { echo("selected"); } ?>  value="8">Store Inventory Admin</option>
                                    <?php } ?>
                                    <?php if (session('user_role') == 4) { ?>
                                        <option <?php if ($employee_data["MAEMP_ROLE"] == 6) { echo("selected"); } ?>  value="6">Production Staff</option>
                                    <?php } ?>
                                    <?php if (session('user_role') == 5) { ?>
                                        <option <?php if ($employee_data["MAEMP_ROLE"] == 7) { echo("selected"); } ?>  value="7">Packaging Staff</option>
                                    <?php } ?>
                                    <?php if (session('user_role') == 8) { ?>
                                        <option <?php if ($employee_data["MAEMP_ROLE"] == 9) { echo("selected"); } ?>  value="9">Store Staff</option>
                                    <?php } ?>
                                <?php } ?>
                                <?php if (session('brand_type') == 2) { ?>
                                    <?php if (session('user_role') == 3) { ?>
                                        <option <?php if ($employee_data["MAEMP_ROLE"] == 4) { echo("selected"); } ?>  value="4">Test Lab Doctor</option>
                                        <option <?php if ($employee_data["MAEMP_ROLE"] == 5) { echo("selected"); } ?>  value="5">Laboratorium Doctor</option>
                                        <option <?php if ($employee_data["MAEMP_ROLE"] == 8) { echo("selected"); } ?>  value="8">Result Doctor</option>
                                    <?php } ?>
                                    <?php if (session('user_role') == 4) { ?>
                                        <option <?php if ($employee_data["MAEMP_ROLE"] == 6) { echo("selected"); } ?>  value="6">Test Lab Staff</option>
                                    <?php } ?>
                                    <?php if (session('user_role') == 5) { ?>
                                        <option <?php if ($employee_data["MAEMP_ROLE"] == 7) { echo("selected"); } ?>  value="7">Laboratorium Staff</option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <br><h4>2. Login Account Data</h4><br>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Account Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="employee_username" placeholder="Input account name" value="{{ $employee_data["MAEMP_USER_NAME"] }}">
                        </div>
                        <div class="col-md-4">
                            <label>Account Email:</label>
                            <input type="text" readonly maxlength="255" class="form-control" name="employee_email" placeholder="Input email" value="{{ $employee_data["MAEMP_EMAIL"] }}">
                        </div>
                        <div class="col-md-4">
                            <label>Phone Number:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select class="form-control select2 country_code" name="employee_phone_code">
                                        <?php for ($i = 0; $i < count($code); $i++) { ?>
                                            <option value="<?php echo $code[$i]["MACOP_CODE"] ?>">
                                                <?php echo $code[$i]["MACOP_CODE"] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                &nbsp;
                                <input type="text" maxlength="15"  class="form-control numeric_input phone_number" name="employee_phone" placeholder="Input phone number" value="{{ $employee_data["MAEMP_PHONE_NUMBER"] }}">
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
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route('master_data_employee_view') }}" class="btn btn-secondary">Back</a>
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
