@extends('layouts.app')

@section('page_title', 'Edit Product Model Form')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product_model/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/product_model/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit Product Model Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_product_model_update") }}" data-form-success-redirect="{{ route("master_data_product_model_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MPRMO_CODE" value="{{ $data['MPRMO_CODE'] }}">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Model Code:</label>
                            <input type="text" readonly class="form-control" value="{{ $data['MPRMO_CODE'] }}">
                        </div>
                        <div class="col-lg-3">
                            <label>Brand:</label>
                            <input type="hidden" id="brand" value="<?php echo session('brand_code'); ?>">
                            <input type="text" readonly class="form-control" value="<?php echo session('brand_name'); ?>">
                        </div>
                        <div class="col-lg-3">
                            <label>Category:</label>
                            <input type="hidden" name="MPRMO_MPRCA_CODE" class="form-control" value="<?php echo $data["MPRMO_MPRCA_CODE"]; ?>">
                            <input type="text" readonly class="form-control" value="<?php echo $data["MPRMO_MPRCA_TEXT"]; ?>">
                        </div>
                        <div class="col-lg-3">
                            <label>Product:</label>
                            <input type="hidden" name="MPRMO_MPRDT_CODE" class="form-control" value="<?php echo $data["MPRMO_MPRDT_CODE"]; ?>">
                            <input type="text" readonly class="form-control" value="<?php echo $data["MPRMO_MPRDT_TEXT"]; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-9">
                            <label>Model Name:</label>
                            <input type="text" class="form-control" name="MPRMO_TEXT" value="{{ $data['MPRMO_TEXT'] }}" placeholder="Input model name">
                        </div>
                        <div class="col-lg-3">
                            <label class="mb-5">Status:</label>
                            <div class="radio-inline">
                                <label class="radio">
                                    <input type="radio" value="1" <?php if ($data["MPRMO_STATUS"] === 1) {
                                        echo("checked");
                                    } ?> name="MPRMO_STATUS">
                                <span></span>Active</label>
                                &nbsp;&nbsp;
                                <label class="radio">
                                    <input type="radio" value="0" <?php if ($data["MPRMO_STATUS"] === 0) {
                                        echo("checked");
                                    } ?> name="MPRMO_STATUS">
                                <span></span>Inactive</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_product_model_view") }}" class="btn btn-secondary">Back</a>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button type="button" id="submit_btn" class="btn btn-primary mr-2">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
