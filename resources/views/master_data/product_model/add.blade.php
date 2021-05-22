@extends('layouts.app')

@section('page_title', 'Add Product Model Form')

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
                <h3 class="card-title">Add Product Model Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_product_model_save") }}" data-form-success-redirect="{{ route("master_data_product_model_view") }}" enctype="multipart/form-data">
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
                            <select class="form-control select2" id="category" name="MPRMO_MPRCA_CODE">
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
                            <select class="form-control select2" id="product" name="MPRMO_MPRDT_CODE">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Model Name:</label>
                            <input type="text" class="form-control" name="MPRMO_TEXT" placeholder="Input Model Name">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_product_model_view") }}" class="btn btn-secondary">Back</a>
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
