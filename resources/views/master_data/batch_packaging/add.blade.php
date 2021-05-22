@extends('layouts.app')

@section('page_title', 'Batch Packaging ')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_packaging/batch_production.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_packaging/date.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_packaging/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_packaging/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Create Batch Acceptance Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_batch_packaging_save") }}" data-form-success-redirect="{{ route("master_data_batch_packaging_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Product Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Batch Production:</label>
                            <select class="form-control select2" id="batch_production" name="MABPA_MABPR_CODE">
                                <option></option>
                                <?php for ($j = 0; $j < count($closed_batch_production); $j++) { ?>
                                    <option value="<?php echo $closed_batch_production[$j]["MABPR_CODE"] ?>"><?php echo $closed_batch_production[$j]["MABPR_TEXT"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label>Brand Product:</label>
                            <input disabled type="text" value="" id="brand_product" class="form-control">
                        </div>
                        <div class="col-lg-3">
                            <label>Category Product:</label>
                            <input disabled type="text" value="" id="category_product" class="form-control">
                        </div>
                        <div class="col-lg-3">
                            <label>Product:</label>
                            <input disabled type="text" value="" id="product" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Model Product:</label>
                            <input disabled type="text" value="" id="model_product" class="form-control">
                        </div>
                        <div class="col-lg-3">
                            <label>Version Product:</label>
                            <input disabled type="text" value="" id="version_product" class="form-control">
                        </div>
                        <div class="col-lg-3">
                            <label>Paired Quantity:</label>
                            <input disabled type="text" value="" id="paired_quantity_product" class="form-control">
                        </div>
                        <div class="col-lg-3">
                            <label>Packaging Center:</label>
                            <input disabled type="text" value="" id="plant_product" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Notes:</label>
                            <textarea class="form-control" name="MABPA_NOTES" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_batch_packaging_view") }}" class="btn btn-secondary">Back</a>
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