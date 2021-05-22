@extends('layouts.app')

@section('page_title', 'Edit Product Version Form')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product_version/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/product_version/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/product_version/sku_edit.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit Product Version Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_product_version_update") }}" data-form-success-redirect="{{ route("master_data_product_version_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MPRVE_CODE" value="{{ $data['MPRVE_CODE'] }}">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Version Code:</label>
                            <input readonly type="text" class="form-control" value="{{ $data['MPRVE_CODE'] }}">
                        </div>
                        <div class="col-lg-4">
                            <label>Brand:</label>
                            <input type="hidden" id="brand" value="<?php echo session('brand_code'); ?>">
                            <input type="text" readonly class="form-control" value="<?php echo session('brand_name'); ?>">
                        </div>
                        <div class="col-lg-4">
                            <label>Category:</label>
                            <input type="hidden" name="MPRVE_MPRCA_CODE" class="form-control" value="<?php echo $data["MPRVE_MPRCA_CODE"]; ?>">
                            <input type="text" readonly class="form-control" id="category_edit" value="<?php echo $data["MPRVE_MPRCA_TEXT"]; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Product:</label>
                            <input type="hidden" name="MPRVE_MPRDT_CODE" class="form-control" value="<?php echo $data["MPRVE_MPRDT_CODE"]; ?>">
                            <input type="text" readonly class="form-control" id="product_edit" value="<?php echo $data["MPRVE_MPRDT_TEXT"]; ?>">
                        </div>
                        <div class="col-lg-4">
                            <label>Model:</label>
                            <input type="hidden" name="MPRVE_MPRMO_CODE" class="form-control" value="<?php echo $data["MPRVE_MPRMO_CODE"]; ?>">
                            <input type="text" readonly class="form-control" id="model_edit" value="<?php echo $data["MPRVE_MPRMO_TEXT"]; ?>">
                        </div>
                        <div class="col-lg-4">
                            <label>Version Name:</label>
                            <input type="text" class="form-control" name="MPRVE_TEXT" id="version_edit" value="{{ $data['MPRVE_TEXT'] }}" placeholder="Example: Model 01">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Product SKU:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="sku" value="{{ $data['MPRVE_SKU'] }}" name="MPRVE_SKU" placeholder="SAIDLAO">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" id="generate" type="button">Generate</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="mb-5">Status:</label>
                            <div class="radio-inline">
                                <label class="radio">
                                    <input type="radio" value="1" <?php if ($data["MPRVE_STATUS"] === 1) {
                                        echo("checked");
                                    } ?> name="MPRVE_STATUS">
                                <span></span>Active</label>
                                &nbsp;&nbsp;
                                <label class="radio">
                                    <input type="radio" value="0" <?php if ($data["MPRVE_STATUS"] === 0) {
                                        echo("checked");
                                    } ?> name="MPRVE_STATUS">
                                <span></span>Inactive</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Product Description<span style="color:red">*</span>:</label>
                            <textarea type="text" id="product_desc" class="form-control" rows="5" name="MPRVE_NOTES" placeholder="Example: Version 01">{{ $data["MPRVE_NOTES"] }}</textarea>
                            <span id="product_desc_label" style="color:red">* Maximum 255 characters</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_product_version_view") }}" class="btn btn-secondary">Back</a>
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
