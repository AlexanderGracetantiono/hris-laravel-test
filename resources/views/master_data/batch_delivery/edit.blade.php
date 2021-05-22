@extends('layouts.app')

@section('page_title', 'Master Batch Delivery')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_delivery/date.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_delivery/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_delivery/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_delivery/repeater_edit.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_delivery/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_delivery/activation_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Activate Batch Delivery Form</h3>
                <div class="card-toolbar">
                    <button type="button" data-action="{{ route('master_data_batch_delivery_activate') }}" class="btn btn-success btn-pill float-right mr-2 activation_btn"> <i class="la la-check"></i>Activate batch</button>
                </div>
            </div>
            <form class="form" id="form_activate" method="POST" data-form-success-redirect="{{ route('master_data_batch_delivery_view') }}">
                @csrf
                <input type="hidden" name="SUBPA_CODE" value="<?php echo $data["SUBPA_CODE"]; ?>">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h6 style="color:red">If you have made any changes on the edit form, please save the changes first before activating the batch !</h6>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Delivery Notes:</label>
                            <textarea <?php if ($data["SUBPA_ACTIVATION_STATUS"] == 2) { echo("disabled");} ?> rows="5" class="form-control" name="SUBPA_NOTES"><?php echo $data["SUBPA_NOTES"] ?></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit Batch Delivery Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_batch_delivery_update") }}" data-form-success-redirect="{{ route("master_data_batch_delivery_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="SUBPA_CODE" value="<?php echo $data["SUBPA_CODE"]; ?>">
                <input type="hidden" name="old_qty" value="<?php echo $data["SUBPA_QTY"]; ?>">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Pool Product Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Pool Production:</label>
                            <input type="hidden" value="<?php echo($selected_pool_product["POPRD_CODE"]) ?>" name="SUBPA_POPRD_CODE">
                            <input disabled type="text" class="form-control" value="<?php echo($selected_pool_product["POPRD_MPRVE_SKU"]) ?>">
                        </div>
                        <div class="col-lg-6">
                            <label>Remain Batch Quantity:</label>
                            <input readonly type="text" id="packaging_qty" value="<?php echo($selected_pool_product["POPRD_QTY_LEFT"]) ?>" class="form-control numeric_input">
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
                            <input disabled type="text" value="<?php echo $selected_pool_product["POPRD_MPRCA_TEXT"]; ?>" class="form-control" id="category" />
                        </div>
                        <div class="col-lg-6">
                            <label>Product:</label>
                            <input disabled type="text" value="<?php echo $selected_pool_product["POPRD_MPRDT_TEXT"]; ?>" class="form-control" id="product" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Model:</label>
                            <input disabled type="text" value="<?php echo $selected_pool_product["POPRD_MPRMO_TEXT"]; ?>" class="form-control" id="model" />
                        </div>
                        <div class="col-lg-6">
                            <label>Version:</label>
                            <input disabled type="text" value="<?php echo $selected_pool_product["POPRD_MPRVE_TEXT"]; ?>" class="form-control" id="version" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>SKU:</label>
                            <input disabled type="text" value="<?php echo $selected_pool_product["POPRD_MPRVE_SKU"]; ?>" class="form-control" id="sku" />
                        </div>
                        <div class="col-lg-6">
                            <label>Product Description:</label>
                            <textarea disabled rows="5" class="form-control" id="notes"><?php echo $selected_pool_product["POPRD_MPRVE_NOTES"]; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>3. Batch Delivery Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Batch Name:</label>
                            <input type="text" class="form-control" value="<?php echo $data["SUBPA_TEXT"]; ?>" name="SUBPA_TEXT" placeholder="Input Batch Name">
                        </div>
                        <div class="col-lg-4">
                            <label>Packaging Center:</label>
                            <select class="form-control select2" id="plant" name="SUBPA_MAPLA_CODE">
                            <option ></option>
                                <?php for ($j = 0; $j < count($plants); $j++) { ?>
                                    <option <?php if ($data["SUBPA_MAPLA_CODE"] == $plants[$j]["MAPLA_CODE"]) { echo("selected"); } ?> value="<?php echo $plants[$j]["MAPLA_CODE"] ?>"><?php echo $plants[$j]["MAPLA_TEXT"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label>Batch Quantity:</label>
                            <input type="text" class="form-control numeric_input" value="<?php echo $data["SUBPA_QTY"]; ?>" name="SUBPA_QTY" placeholder="Input Batch Quantity">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Scheduled Batch Start Date:</label>
                            <input disabled type="text" class="form-control" data-date-format="yyyy-mm-dd" name="SUBPA_DATE_START" id="SUBPA_DATE_START" value="<?php echo substr($data["SUBPA_START_TIMESTAMP"],0,10) ?>" placeholder="Select date" />
                        </div>
                        <div class="col-lg-4">
                            <label>Scheduled Batch Start Time:</label>
                            <input disabled class="form-control" id="SUBPA_TIME_START" name="SUBPA_TIME_START" placeholder="Select time" value="<?php echo substr($data["SUBPA_START_TIMESTAMP"],11,5) ?>" type="text"/>
                        </div>
                        <!-- <div class="col-lg-4">
                            <label>Batch End Date:</label>
                            <input type="text" class="form-control" data-date-format="yyyy-mm-dd" name="SUBPA_DATE_END" id="SUBPA_DATE_END" placeholder="Select date" />
                        </div> -->
                        <div class="col-lg-4">
                            <label>Scheduled Batch End Time:</label>
                            <input disabled class="form-control" id="SUBPA_TIME_END" name="SUBPA_TIME_END" placeholder="Select time" value="<?php echo substr($data["SUBPA_END_TIMESTAMP"],11,5) ?>" type="text"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>4. Assign Staff Delivery</h4>
                        </div>
                    </div>
                    <?php for ($i=0; $i < count($staff_packaging); $i++) { ?>
                        <div class="form-group row">
                            <div class="col-lg-10">
                                <label>Employee:</label>
                                <input type="hidden" name="OLD_MAEMP_CODE[]" value="<?php echo $staff_packaging[$i]["STBPA_EMP_CODE"] ?>">
                                <input disabled type="text" class="form-control" value="<?php echo $staff_packaging[$i]["STBPA_EMP_TEXT"] ?>">
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label><br>
                                <button data-code="<?php echo ($staff_packaging[$i]["STBPA_ID"]); ?>" data-action="{{ route('master_data_batch_delivery_delete_staff') }}" class="btn btn-sm font-weight-bolder delete_btn btn-light-danger"><i class="la la-trash-o"></i>Delete</button>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group row">
                        <div id="repeater" class="col-md-12">
                            <div id="repeater">
                                <div data-repeater-list="STAFF">
                                    <div data-repeater-item class="form-group row align-items-center">
                                        <div class="col-lg-10">
                                            <label>Employee:</label>
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
                            <a href="{{ route("master_data_batch_delivery_view") }}" class="btn btn-secondary">Back</a>
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