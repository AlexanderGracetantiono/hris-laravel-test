@extends('layouts.app')

@section('page_title', 'Edit Product Form')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/product/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit Product Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_product_update") }}" data-form-success-redirect="{{ route("master_data_product_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MPRDT_ID" value="<?php echo $data["MPRDT_ID"]; ?>">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Brand:</label>
                            <input type="text" readonly class="form-control" value="<?php echo session('brand_name'); ?>">
                        </div>
                        <div class="col-lg-4">
                            <label>Product Code:</label>
                            <input type="text" readonly maxlength="255" class="form-control" name="MPRDT_CODE" value="<?php echo $data["MPRDT_CODE"]; ?>" placeholder="Input product code">
                        </div>
                        <div class="col-lg-4">
                            <label>Category:</label>
                            <input type="hidden" name="MPRDT_MPRCA_CODE" class="form-control" value="<?php echo $data["MPRDT_MPRCA_CODE"]; ?>">
                            <input type="text" readonly class="form-control" value="<?php echo $data["MPRDT_MPRCA_TEXT"]; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-8">
                            <label>Product Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="MPRDT_TEXT" value="<?php echo $data["MPRDT_TEXT"]; ?>" placeholder="Input product name">
                        </div>
                        <div class="col-lg-4">
                            <label class="mb-5">Status:</label>
                            <div class="radio-inline">
                                <label class="radio">
                                    <input type="radio" value="1" <?php if ($data["MPRDT_STATUS"] === 1) {
                                        echo("checked");
                                    } ?> name="MPRDT_STATUS">
                                <span></span>Active</label>
                                &nbsp;&nbsp;
                                <label class="radio">
                                    <input type="radio" value="0" <?php if ($data["MPRDT_STATUS"] === 0) {
                                        echo("checked");
                                    } ?> name="MPRDT_STATUS">
                                <span></span>Inactive</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_product_view") }}" class="btn btn-secondary">Back</a>
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
