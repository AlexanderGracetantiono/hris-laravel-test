@extends('layouts.app')

@section('page_title', 'Batch Store ')

@push('styles')

@endpush

@push('scripts')
    <script src="{{ asset('custom/js/master_data/batch_store/sub_batch_packaging.js') }}"></script>
    <script src="{{ asset('custom/js/master_data/batch_store/date.js') }}"></script>
    <script src="{{ asset('custom/js/master_data/batch_store/select2.js') }}"></script>
    <script src="{{ asset('custom/js/master_data/batch_store/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit Batch Store Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route('master_data_batch_store_update') }}" data-form-success-redirect="{{ route("master_data_batch_store_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MBSTR_CODE" value="{{ $data['MBSTR_CODE'] }}">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Batch Packaging Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Batch Packaging:</label>
                            <input disabled type="text" value="<?php echo $data["MBSTR_MBRAN_TEXT"] ?>" id="brand_product" class="form-control">
                        </div>
                        <div class="col-lg-4">
                            <label>Quantity:</label>
                            <input disabled type="text" value="<?php echo $data["MBSTR_SUBPA_QTY"] ?>" id="paired_quantity_product" class="form-control">
                        </div>
                        <div class="col-lg-4">
                            <label>Packaging Center:</label>
                            <input disabled type="text" value="<?php echo $data["MBSTR_MAPLA_TEXT"] ?>" id="paired_quantity_product" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>2. Product Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Category:</label>
                            <input disabled type="text" value="<?php echo $data["MBSTR_MPRCA_TEXT"] ?>" id="category_product" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label>Product:</label>
                            <input disabled type="text" value="<?php echo $data["MBSTR_MPRDT_TEXT"] ?>" id="product" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Model:</label>
                            <input disabled type="text" value="<?php echo $data["MBSTR_MPRMO_TEXT"] ?>" id="model_product" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label>Version:</label>
                            <input disabled type="text" value="<?php echo $data["MBSTR_MPRVE_TEXT"] ?>" id="version_product" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>SKU:</label>
                            <input disabled type="text" value="<?php echo $data["MBSTR_MPRVE_SKU"] ?>" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label>Product Description:</label>
                            <textarea disabled rows="5" type="text" class="form-control"><?php echo $data["MBSTR_MPRVE_NOTES"] ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>3. Batch Store Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Batch Store Date:</label>
                            <input type="text" class="form-control" data-date-format="yyyy-mm-dd" value="<?php echo $data["MBSTR_DATE"] ?>" name="MBSTR_DATE" id="MABPA_DATE" placeholder="Select date"/>
                        </div>
                        <div class="col-lg-6">
                            <label>Batch Store Name:</label>
                            <input type="text" class="form-control" name="MBSTR_TEXT" value="<?php echo $data["MBSTR_TEXT"] ?>" placeholder="Input batch name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Store Notes:</label>
                            <textarea rows="5" class="form-control" name="MBSTR_NOTES" placeholder="Input batch notes"><?php echo $data["MBSTR_NOTES"] ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_batch_store_view") }}" class="btn btn-secondary">Back</a>
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
