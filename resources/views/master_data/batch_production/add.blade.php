@extends('layouts.app')

@section('page_title', 'Master Batch Production')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_production/date.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/repeater.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/get_staff_production.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Create Batch Production Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_batch_production_save") }}" data-form-success-redirect="{{ route("master_data_batch_production_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Batch Production Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Batch Name:</label>
                            <input type="text" class="form-control" name="MABPR_TEXT" placeholder="Input Batch Name">
                        </div>
                        <div class="col-lg-4">
                            <label>Targeted Quantity:</label>
                            <input type="text" class="form-control numeric_input" name="MABPR_EXPECTED_QTY" placeholder="Input Batch Targeted Quantity">
                        </div>
                        <div class="col-lg-4">
                            <label>Production Center:</label>
                            <select class="form-control select2" id="plant_select2" name="MABPR_MAPLA_CODE">
                                <option></option>
                                <?php for ($j = 0; $j < count($master_plant); $j++) { ?>
                                    <?php if ($master_plant[$j]["MAPLA_TYPE"] == 1) { ?>
                                        <option value="<?php echo $master_plant[$j]["MAPLA_CODE"] ?>"><?php echo $master_plant[$j]["MAPLA_TEXT"] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Scheduled Batch Start Date:</label>
                            <input type="text" class="form-control" data-date-format="yyyy-mm-dd" name="MABPR_DATE_START" id="MABPR_DATE_START" placeholder="Select date" />
                        </div>
                        <div class="col-lg-4">
                            <label>Scheduled Batch Start Time:</label>
                            <input class="form-control" id="MABPR_TIME_START" name="MABPR_TIME_START" placeholder="Select time" type="text"/>
                        </div>
                        <!-- <div class="col-lg-4">
                            <label>Batch End Date:</label>
                            <input type="text" class="form-control" data-date-format="yyyy-mm-dd" name="MABPR_DATE_END" id="MABPR_DATE_END" placeholder="Select date" />
                        </div> -->
                        <div class="col-lg-4">
                            <label>Scheduled Batch End Time:</label>
                            <input class="form-control" id="MABPR_TIME_END" name="MABPR_TIME_END" placeholder="Select time" type="text"/>
                        </div>
                        <div class="col-lg-8">
                        </div>
                        <div class="col-lg-4">
                            <span id="err_message" <span style="color:red">* End time must be greater than start time</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br>
                            <h4>2. Product Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <input type="hidden" id="brand_select2" value="<?php echo session("brand_code"); ?>">
                        <div class="col-lg-6">
                            <label>Category:</label>
                            <select class="form-control select2" id="product_category_select2" name="MABPR_MPRCA_CODE">
                                <option></option>
                                <?php for ($j = 0; $j < count($master_product_category); $j++) { ?>
                                    <option value="<?php echo $master_product_category[$j]["MPRCA_CODE"] ?>"><?php echo $master_product_category[$j]["MPRCA_TEXT"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label>Product:</label>
                            <select class="form-control select2" id="product_select2" name="MABPR_MPRDT_CODE">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Model:</label>
                            <select class="form-control select2" id="product_model_select2" name="MABPR_MPRMO_CODE">
                                <option></option>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label>Version:</label>
                            <select class="form-control select2" id="product_version_select2" name="MABPR_MPRVE_CODE">
                                <option></option>
                            </select>
                        </div>

                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>SKU:</label>
                            <input type="text" class="form-control" id="sku" disabled placeholder="Product SKU">
                        </div>
                        <div class="col-lg-6">
                            <label>Product Description:</label>
                            <textarea class="form-control" id="description" rows="4" disabled placeholder="Product Description"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br>
                            <h4>3. Assign User Production</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div id="repeater" class="col-md-12">
                            <div id="repeater">
                                <div data-repeater-list="STAFF">
                                    <div data-repeater-item class="form-group row align-items-center">
                                        <div class="col-lg-10">
                                            <label>User:</label>
                                            <select class="form-control select2 employee" name="MAEMP_CODE">
                                                <option></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label><br>
                                            <a href="javascript:;" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger">
                                                <i class="la la-trash-o"></i>Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row col-md-12">
                                    <label>&nbsp;</label><br>
                                    <a href="javascript:;" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-primary">
                                        <i class="la la-plus"></i>Add
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_batch_production_view") }}" class="btn btn-secondary">Back</a>
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