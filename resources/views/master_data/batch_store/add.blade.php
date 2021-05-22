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
                <h3 class="card-title">Create Batch Store Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_batch_store_save") }}" data-form-success-redirect="{{ route("master_data_batch_store_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Sub Batch Packaging Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Sub Batch Packaging:</label>
                            <select class="form-control select2" id="sub_batch_packaging" name="MBSTR_SUBPA_CODE">
                                <option></option>
                                <?php for ($j=0 ; $j < count($closed_sub_batch_packaging); $j++) {?>
                                    <option value="<?php echo $closed_sub_batch_packaging[$j]["SUBPA_CODE"] ?>"><?php echo $closed_sub_batch_packaging[$j]["SUBPA_TEXT"] ?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label>Quantity:</label>
                            <input disabled type="text" value="" id="paired_quantity_product" class="form-control">
                        </div>
                        <div class="col-lg-4">
                            <label>Packaging Center:</label>
                            <input disabled type="text" value="" id="plant" class="form-control">
                        </div>
                        
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Production Notes:</label>
                            <textarea disabled rows="5" class="form-control" id="batch_production_notes"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Acceptance Notes:</label>
                            <textarea disabled rows="5" class="form-control" id="batch_acceptance_notes"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Sub Batch Packaging Notes:</label>
                            <textarea disabled rows="5" class="form-control" id="sub_batch_packaging_notes"></textarea>
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
                            <input disabled type="text" value="" id="category_product" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label>Product:</label>
                            <input disabled type="text" value="" id="product" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Model:</label>
                            <input disabled type="text" value="" id="model_product" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label>Version:</label>
                            <input disabled type="text" value="" id="version_product" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>3. Batch Store Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Batch Store Name:</label>
                            <input type="text" class="form-control" name="MBSTR_TEXT" placeholder="Input batch name">
                        </div>
                        <div class="col-lg-6">
                            <label>Batch Store Date:</label>
                            <input type="text" id="MABPA_DATE" data-date-format="yyyy-mm-dd" class="form-control" name="MBSTR_DATE" placeholder="Input batch date">
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
