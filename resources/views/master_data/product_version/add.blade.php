@extends('layouts.app')

@section('page_title', 'Add Product Version Form')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product_version/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/product_version/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/product_version/sku.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Add Product Version Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_product_version_save") }}" data-form-success-redirect="{{ route("master_data_product_version_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Brand:</label>
                            <input type="hidden" id="brand" value="<?php echo session('brand_code'); ?>">
                            <input disabled type="text" class="form-control" value="<?php echo session('brand_name'); ?>">
                        </div>
                        <div class="col-lg-4">
                            <label>Category:</label>
                            <select class="form-control select2" id="category" name="MPRVE_MPRCA_CODE">
                                <option></option>
                                <?php for ($i = 0; $i < count($category); $i++) { ?>
                                    <option value="<?php echo $category[$i]["MPRCA_CODE"] ?>">
                                        <?php echo $category[$i]["MPRCA_TEXT"] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label>Product:</label>
                            <select class="form-control select2" id="product" name="MPRVE_MPRDT_CODE">
                                <option></option>
                                <?php for ($i = 0; $i < count($product); $i++) { ?>
                                    <option value="<?php echo $product[$i]["MPRDT_CODE"] ?>">
                                        <?php echo $product[$i]["MPRDT_TEXT"] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Model:</label>
                            <select class="form-control select2" id="model" name="MPRVE_MPRMO_CODE">
                                <option></option>
                                <?php for ($i = 0; $i < count($model); $i++) { ?>
                                    <option value="<?php echo $model[$i]["MPRMO_CODE"] ?>">
                                        <?php echo $model[$i]["MPRMO_TEXT"] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label>Version Name:</label>
                            <input type="text" class="form-control" id="version" name="MPRVE_TEXT" placeholder="Example: Version 01">
                        </div>
                        <div class="col-lg-4">
                            <label>Product SKU:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="sku" name="MPRVE_SKU" placeholder="SAIDLAO">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" id="generate" type="button">Generate</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Product Description<span style="color:red">*</span>:</label>
                            <textarea type="text" class="form-control" rows="5" name="MPRVE_NOTES" placeholder="Example: Version 01">
                            </textarea>
                            <span style="color:red">* Maximum 255 characters</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_product_version_view") }}" class="btn btn-secondary">Back</a>
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
