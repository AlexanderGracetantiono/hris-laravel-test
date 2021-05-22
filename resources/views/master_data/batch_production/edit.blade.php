@extends('layouts.app')

@section('page_title', 'Master Batch Production')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_production/date.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/repeater_edit.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/get_staff_production.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/activation_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Activate Batch Production</h3>
                <div class="card-toolbar">
                    <button type="button" data-action="{{ route('master_data_batch_production_activate') }}" class="btn btn-success float-right btn-pill mr-2 activation_btn"> <i class="la la-check"></i>Activate batch</button>
                </div>
            </div>
            <div class="card-body">
                <form class="form" id="form_activation" method="POST" data-form-success-redirect="{{ route('master_data_batch_production_view') }}">
                    @csrf
                    <input type="hidden" name="MABPR_CODE" value="{{ $data['MABPR_CODE'] }}">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h6 style="color:red">If you have made any changes on the edit form, please save the changes first before activating the batch !</h6>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Production Notes:</label>
                            <textarea <?php if ($data["MABPR_ACTIVATION_STATUS"] == 2) { echo("disabled");} ?> rows="5" class="form-control" name="MABPR_NOTES"><?php echo $data["MABPR_NOTES"] ?></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit Batch Production Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_batch_production_update") }}" data-form-success-redirect="{{ route("master_data_batch_production_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MABPR_CODE" value="<?php echo $data["MABPR_CODE"]; ?>">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Batch Production Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Batch Name:</label>
                            <input type="text" class="form-control" value="<?php echo $data["MABPR_TEXT"]; ?>" name="MABPR_TEXT" placeholder="Input Batch Name">
                        </div>
                        <div class="col-lg-4">
                            <label>Targeted Quantity:</label>
                            <input type="text" class="form-control numeric_input" value="<?php echo $data["MABPR_EXPECTED_QTY"]; ?>" name="MABPR_EXPECTED_QTY" placeholder="Input Batch Quantity">
                        </div>
                        <div class="col-lg-4">
                            <label>Production Center:</label>
                            <select class="form-control select2" id="plant_select2" name="MABPR_MAPLA_CODE">
                            <option ></option>
                                <?php for ($j = 0; $j < count($master_plant); $j++) { ?>
                                    <option <?php if ($data["MABPR_MAPLA_CODE"] == $master_plant[$j]["MAPLA_CODE"]) { echo("selected"); } ?> value="<?php echo $master_plant[$j]["MAPLA_CODE"] ?>"><?php echo $master_plant[$j]["MAPLA_TEXT"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Scheduled Batch Start Date:</label>
                            <input disabled type="text" class="form-control" data-date-format="yyyy-mm-dd" name="MABPR_DATE_START" id="MABPR_DATE_START" value="<?php echo substr($data["MABPR_START_TIMESTAMP"],0,10) ?>" placeholder="Select date" />
                        </div>
                        <div class="col-lg-4">
                            <label>Scheduled Batch Start Time:</label>
                            <input disabled class="form-control" id="MABPR_TIME_START" name="MABPR_TIME_START" placeholder="Select time" value="<?php echo substr($data["MABPR_START_TIMESTAMP"],11,7) ?>" type="text"/>
                        </div>
                        <!-- <div class="col-lg-4">
                            <label>Scheduled Batch End Date:</label>
                            <input type="text" class="form-control" data-date-format="yyyy-mm-dd" name="MABPR_DATE_END" id="MABPR_DATE_END" placeholder="Select date" />
                        </div> -->
                        <div class="col-lg-4">
                            <label>Scheduled Batch End Time:</label>
                            <input disabled class="form-control" id="MABPR_TIME_END" name="MABPR_TIME_END" placeholder="Select time" value="<?php echo substr($data["MABPR_END_TIMESTAMP"],11,7) ?>" type="text"/>
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
                            <option ></option>
                                <?php for ($j = 0; $j < count($master_product_category); $j++) { ?>
                                    <option <?php if ($data["MABPR_MPRCA_CODE"] == $master_product_category[$j]["MPRCA_CODE"]) { echo("selected"); } ?> value="<?php echo $master_product_category[$j]["MPRCA_CODE"] ?>"><?php echo $master_product_category[$j]["MPRCA_TEXT"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label>Product:</label>
                            <select class="form-control select2" id="product_select2" name="MABPR_MPRDT_CODE">
                            <option ></option>
                                <?php for ($j = 0; $j < count($master_product); $j++) { ?>
                                    <option <?php if ($data["MABPR_MPRDT_CODE"] == $master_product[$j]["MPRDT_CODE"]) { echo("selected"); } ?> value="<?php echo $master_product[$j]["MPRDT_CODE"] ?>"><?php echo $master_product[$j]["MPRDT_TEXT"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Model:</label>
                            <select class="form-control select2" id="product_model_select2" name="MABPR_MPRMO_CODE">
                            <option ></option>
                                <?php for ($j = 0; $j < count($master_product_model); $j++) { ?>
                                    <option <?php if ($data["MABPR_MPRMO_CODE"] == $master_product_model[$j]["MPRMO_CODE"]) { echo("selected"); } ?> value="<?php echo $master_product_model[$j]["MPRMO_CODE"] ?>"><?php echo $master_product_model[$j]["MPRMO_TEXT"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label>Version:</label>
                            <select class="form-control select2" id="product_version_select2" name="MABPR_MPRVE_CODE">
                            <option ></option>
                                <?php for ($j = 0; $j < count($master_product_version); $j++) { ?>
                                    <option <?php if ($data["MABPR_MPRVE_CODE"] == $master_product_version[$j]["MPRVE_CODE"]) { echo("selected"); } ?> value="<?php echo $master_product_version[$j]["MPRVE_CODE"] ?>"><?php echo $master_product_version[$j]["MPRVE_TEXT"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>SKU:</label>
                            <input type="text" class="form-control" value="{{ $data['MABPR_MPRVE_SKU'] }}" id="sku" disabled placeholder="Product SKU">
                        </div>
                        <div class="col-lg-6">
                            <label>Product Description:</label>
                            <textarea class="form-control" id="description" rows="4" disabled placeholder="Product Description">{{ $data['MABPR_MPRVE_NOTES'] }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>3. Assign User Production</h4>
                        </div>
                    </div>
                    <?php for ($i=0; $i < count($staff_production); $i++) { ?>
                        <div class="form-group row">
                            <div class="col-lg-10">
                                <label>User:</label>
                                <input type="hidden" class="form-control" name="OLD_MAEMP_CODE[]" value="<?php echo $staff_production[$i]["STBPR_EMP_CODE"] ?>">
                                <input type="text" disabled class="form-control" value="<?php echo $staff_production[$i]["STBPR_EMP_TEXT"] ?>">
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label><br>
                                <button data-code="<?php echo ($staff_production[$i]["STBPR_ID"]); ?>" data-action="{{ route('master_data_batch_production_delete_staff') }}" class="btn btn-sm font-weight-bolder delete_btn btn-light-danger"><i class="la la-trash-o"></i>Delete</button>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group row">
                        <div id="repeater" class="col-md-12">
                            <div id="repeater">
                                <div data-repeater-list="STAFF">
                                    <div data-repeater-item class="form-group row align-items-center">
                                        <div class="col-lg-10">
                                            <label>User:</label>
                                            <select class="form-control select2 employee" name="MAEMP_CODE">
                                            <option ></option>
                                                <?php for ($j = 0; $j < count($master_employee); $j++) { ?>
                                                    <option value="<?php echo $master_employee[$j]["MAEMP_CODE"] ?>"><?php echo $master_employee[$j]["MAEMP_TEXT"] ?></option>
                                                <?php } ?>
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
