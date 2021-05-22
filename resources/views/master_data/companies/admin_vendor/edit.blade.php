@extends('layouts.app')

@section('page_title', 'Edit Admin Vendor')

@push('styles')

@endpush

@push('scripts')
{{-- <script src="{{ asset('custom/js/master_data/companies/add/image_upload.js') }}"></script> --}}
<script src="{{ asset('custom/js/master_data/admin_vendor/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/admin_vendor/edit/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Form Edit Admin Vendor</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_admin_vendor_update") }}" data-form-success-redirect="{{ route("master_data_admin_vendor_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Admin Vendor Account Name:</label>
                            <input readonly type="text" maxlength="50" class="form-control max_length_input remove_space" name="maadmin_username" placeholder="Example: adminvendor" value="{{ $maadmin_data["maadmin_username"] }}">
                        </div>
                        <div class="col-lg-4">
                            <label>Admin Vendor Full Name:</label>
                            <input type="text" maxlength="255" class="form-control max_length_input" name="maadmin_real_name" placeholder="Example: Louis Hanberg" value="{{ $maadmin_data["maadmin_real_name"] }}">
                        </div>
                        <div class="col-lg-4">
                            <label>Admin Vendor E-mail:</label>
                            <input type="email" maxlength="50" class="form-control max_length_input" name="maadmin_email" placeholder="Example: louishanberg@gmail.com" value="{{ $maadmin_data["maadmin_email"] }}">
                        </div>
                       
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Admin Vendor Phone Number:</label>
                            <input type="text" maxlength="15" class="form-control max_length_input" name="maadmin_phone" placeholder="Example: 0812xxx" value="{{ $maadmin_data["maadmin_phone"] }}">
                        </div>
                        <div class="col-lg-3">
                            <label>Admin Vendor Gender:</label>
                            <select class="form-control select2" id="maadmin_sex" name="maadmin_sex">
                                <option></option>
                                <option value="1" @if ($maadmin_data["maadmin_sex"] == 1)
                                    selected
                                @endif>Male</option>
                                <option value="2" @if ($maadmin_data["maadmin_sex"] == 2)
                                selected
                            @endif>Female</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label>Admin Company Status:</label>
                            <div class="radio-inline">
                                <label class="radio radio-md radio-rounded">
                                    <input type="radio" name="maadmin_status" value="1" @if ($maadmin_data["maadmin_status"] == 1)
                                        checked
                                    @endif/>
                                    <span></span>
                                    Active
                                </label>
                                <label class="radio radio-md radio-rounded">
                                    <input type="radio" name="maadmin_status" value="0" @if ($maadmin_data["maadmin_status"] == 0)
                                        checked
                                    @endif/>
                                    <span></span>
                                    Inactive
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label>Vendor:</label>
                            <select class="form-control select2" id="maadmin_mavendor_id" name="maadmin_mavendor_id">
                                <option></option>
                                @foreach ($mavendor_data as $row)
                                    <option value="{{ $row["mavendor_id"] }}" @if ($row["mavendor_id"] == $maadmin_data["maadmin_mavendor_id"])
                                        selected
                                    @endif>{{ $row["mavendor_name"] }} - {{ $row["macompany_name"] }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">  
                            <a href="{{ route("master_data_admin_vendor_view") }}" class="btn btn-secondary">Back</a>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button type="submit" class="btn btn-primary mr-2">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
