@extends('layouts.app')

@section('page_title', 'Add User Form')

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
                <h3 class="card-title">Add User Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_employee_save") }}" data-form-success-redirect="{{ route("master_data_employee_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <h4>1. User Data</h4><br>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>User Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="employee_name" placeholder="Input user name">
                        </div>
                        <div class="col-md-6">
                            <label>Role:</label>
                            <select class="form-control select2" id="role_select2" name="role_select2">
                                <option></option>
                                <?php if (session('user_role') == 1) { ?>
                                    <option value="1">CekOri Administrator</option>
                                <?php } ?>
                                <?php if (session('brand_type') == 1) { ?>
                                    <?php if (session('user_role') == 3) { ?>
                                        <option value="4">Production Administrator</option>
                                        <option value="5">Packaging Administrator</option>
                                        <option value="8">Store Inventory Administrator</option>
                                    <?php } ?>
                                    <?php if (session('user_role') == 4) { ?>
                                        <option value="6">Production Staff</option>
                                    <?php } ?>
                                    <?php if (session('user_role') == 5) { ?>
                                        <option value="7">Packaging Staff</option>
                                    <?php } ?>
                                    <?php if (session('user_role') == 8) { ?>
                                        <option value="9">Store Staff</option>
                                    <?php } ?>
                                <?php } ?>
                                <?php if (session('brand_type') == 2) { ?>
                                    <?php if (session('user_role') == 3) { ?>
                                        <option value="4">Testing Lab Doctor</option>
                                        <option value="5">Laboratorium Doctor</option>
                                        <option value="8">Result Doctor</option>
                                    <?php } ?>
                                    <?php if (session('user_role') == 4) { ?>
                                        <option value="6">Testing Lab Staff</option>
                                    <?php } ?>
                                    <?php if (session('user_role') == 5) { ?>
                                        <option value="7">Laboratorium Staff</option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
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
                                <input type="text" maxlength="15"  class="form-control numeric_input phone_number" name="employee_phone" placeholder="Input phone number">
                            </div>
                        </div>
                    </div>
                    <br><h4>2. Login Account Data</h4><br>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>Account Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="employee_username" placeholder="Input account name">
                        </div>
                        <div class="col-md-6">
                            <label>Email:</label>
                            <input type="text" maxlength="255" class="form-control" name="employee_email" placeholder="Input email">
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
