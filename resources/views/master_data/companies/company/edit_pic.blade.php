@extends('layouts.app')

@section('page_title', 'Create Company')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/companies/image_upload.js') }}"></script>
<script src="{{ asset('custom/js/master_data/companies/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Form Edit Company</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_company_update_pic") }}" data-form-success-redirect="{{ route("master_data_company_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MCOMP_CODE" value="<?php echo $data["MCOMP_CODE"]; ?>">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>PIC User Name:</label>
                            <input type="text" class="form-control" name="MAEMP_USER_NAME" placeholder="Example: pic_sanggar_indah">
                        </div>
                        <div class="col-lg-4">
                            <label>PIC Password:</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="col-lg-4">
                            <label>PIC Password Confirmation:</label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_company_view") }}" class="btn btn-secondary">Back</a>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button type="button" id="submit_btn" class="btn btn-primary mr-2">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
