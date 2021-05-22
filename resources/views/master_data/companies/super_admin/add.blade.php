@extends('layouts.app')

@section('page_title', 'Create Super Admin')

@push('styles')

@endpush

@push('scripts')
{{-- <script src="{{ asset('custom/js/master_data/companies/add/image_upload.js') }}"></script> --}}
<script src="{{ asset('custom/js/master_data/super_admin/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/super_admin/add/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Form Create Super Admin</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_super_admin_save") }}" data-form-success-redirect="{{ route("master_data_super_admin_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Super Admin Account Name:</label>
                            <input type="text" maxlength="50" class="form-control max_length_input remove_space" name="maadmin_username" placeholder="Example: superadmin">
                        </div>
                        <div class="col-lg-5">
                            <label>Super Admin Full Name:</label>
                            <input type="text" maxlength="255" class="form-control max_length_input" name="maadmin_real_name" placeholder="Example: Marco Hanberg">
                        </div>
                        <div class="col-lg-4">
                            <label>Super Admin E-mail:</label>
                            <input type="email" maxlength="50" class="form-control max_length_input" name="maadmin_email" placeholder="Example: marcohanberg@gmail.com">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Super Admin Phone Number:</label>
                            <input type="text" maxlength="15" class="form-control max_length_input" name="maadmin_phone" placeholder="Example: 0812xxx">
                        </div>
                        <div class="col-lg-4">
                            <label>Super Admin Gender:</label>
                            <select class="form-control select2" id="maadmin_sex" name="maadmin_sex">
                                <option></option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_super_admin_view") }}" class="btn btn-secondary">Back</a>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button type="submit" class="btn btn-primary mr-2">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
