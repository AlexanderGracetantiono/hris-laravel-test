@extends('layouts.app')

@section('page_title', 'Master Batch Production')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_delivery/date.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_delivery/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_delivery/repeater.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_delivery/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_delivery/get_staff_packaging.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Create Batch Delivery Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route('master_data_batch_delivery_save') }}" data-form-success-redirect="{{ route('master_data_batch_delivery_view') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Pool Product Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Pool Product:</label>
                            <select class="form-control select2" id="pool_product" name="SUBPA_POPRD_CODE">
                                <option ></option>
                                <?php for ($j = 0; $j < count($pool_product); $j++) { ?>
                                    <option value="<?php echo $pool_product[$j]["POPRD_CODE"] ?>"><?php echo $pool_product[$j]["POPRD_MPRVE_SKU"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label>Remaining Pool Product Quantity:</label>
                            <input readonly type="text" id="packaging_qty" class="form-control numeric_input">
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
                            <input disabled type="text" class="form-control" id="category" />
                        </div>
                        <div class="col-lg-6">
                            <label>Product:</label>
                            <input disabled type="text" class="form-control" id="product" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Model:</label>
                            <input disabled type="text" class="form-control" id="model" />
                        </div>
                        <div class="col-lg-6">
                            <label>Version:</label>
                            <input disabled type="text" class="form-control" id="version" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>SKU:</label>
                            <input disabled type="text" class="form-control" id="sku" />
                        </div>
                        <div class="col-lg-6">
                            <label>Product Description:</label>
                            <textarea disabled rows="5" class="form-control" id="notes"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>3. Batch Delivery Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Batch Delivery Name:</label>
                            <input type="text" class="form-control" name="SUBPA_TEXT" placeholder="Input Batch Name">
                        </div>
                        <div class="col-lg-4">
                            <label>Packaging Center:</label>
                            <select class="form-control select2" id="plant" name="SUBPA_MAPLA_CODE">
                                <option ></option>
                                <?php for ($j = 0; $j < count($master_plant); $j++) { ?>
                                    <option value="<?php echo $master_plant[$j]["MAPLA_CODE"] ?>"><?php echo $master_plant[$j]["MAPLA_TEXT"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label>Batch Delivery Quantity:</label>
                            <input type="text" class="form-control numeric_input" name="SUBPA_QTY" placeholder="Input Batch Quantity">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Scheduled Batch Date:</label>
                            <input type="text" class="form-control" data-date-format="yyyy-mm-dd" name="SUBPA_DATE_START" id="SUBPA_DATE_START" placeholder="Select date"/>
                        </div>
                        <div class="col-lg-4">
                            <label>Scheduled Batch Start Time:</label>
                            <input class="form-control" id="SUBPA_TIME_START" name="SUBPA_TIME_START" placeholder="Select time" type="text"/>
                        </div>
                        <div class="col-lg-4">
                            <label>Scheduled Batch End Time:</label>
                            <input class="form-control" id="SUBPA_TIME_END" name="SUBPA_TIME_END" placeholder="Select time" type="text"/>
                        </div>
                        <div class="col-lg-8">
                        </div>
                        <div class="col-lg-4">
                            <span id="err_message" <span style="color:red">* End time must be greater than start time</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>4. Assign User Delivery</h4>
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
                                                <option ></option>
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
