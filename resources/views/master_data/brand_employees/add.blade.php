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
                <h3 class="card-title">Add User Form, Brand : <?php echo $brand["MBRAN_NAME"] ?></h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_employee_brand_save") }}" data-form-success-redirect="{{ route('master_data_employee_brand_view', ['code' => $brand['MBRAN_CODE']]) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="<?php echo $brand["MBRAN_CODE"]; ?>" name="MAEMP_MBRAN_CODE">
                <div class="card-body">
                    <h4>1. User Data</h4><br>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>Brand Name:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $brand["MBRAN_NAME"]; ?>">
                        </div>
                        <div class="col-md-6">
                            <label>Role:</label>
                            <select class="form-control select2" id="role_select2" name="role_select2">
                                <option value="3">PIC Brand</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>User Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="employee_name" placeholder="Input user name">
                        </div>
                        <div class="col-md-6">
                            <label>Phone Number:</label>
                            <div class="input-group">
                                <div class="input-group-prepend mr-3">
                                    <select class="form-control select2 country_code" name="employee_phone_code">
                                        <?php for ($i = 0; $i < count($code); $i++) { ?>
                                            <option value="<?php echo $code[$i]["MACOP_CODE"] ?>">
                                                <?php echo $code[$i]["MACOP_CODE"] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <input type="text" maxlength="15"  class="form-control phone_number numeric_input" name="employee_phone" placeholder="Input phone number">
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
