@extends('layouts.app')

@section('page_title', 'Master Batch Delivery')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_delivery/activation_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Detail Sub Batch Delivery</h3>
            </div>
            <form class="form" id="form" method="POST" data-form-success-redirect="{{ route('master_data_batch_delivery_view') }}">
                @csrf
                <input type="hidden" name="SUBPA_CODE" value="{{ $data['SUBPA_CODE'] }}">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>1. Pool Product Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Pool Product:</label>
                            <input type="hidden" value="<?php echo($selected_batch_packaging["MABPA_CODE"]) ?>" name="SUBPA_MABPA_CODE">
                            <input disabled type="text" class="form-control" value="<?php echo($selected_batch_packaging["MABPA_TEXT"]) ?>">
                        </div>
                        <div class="col-lg-6">
                            <label>Remain Batch Quantity:</label>
                            <input readonly type="text" id="packaging_qty" value="<?php echo($selected_batch_packaging["MABPA_QTY_LEFT"]) ?>" class="form-control numeric_input">
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
                            <input disabled type="text" value="<?php echo $selected_batch_production["MABPR_MPRCA_TEXT"]; ?>" class="form-control" id="category" />
                        </div>
                        <div class="col-lg-6">
                            <label>Product:</label>
                            <input disabled type="text" value="<?php echo $selected_batch_production["MABPR_MPRDT_TEXT"]; ?>" class="form-control" id="product" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Model:</label>
                            <input disabled type="text" value="<?php echo $selected_batch_production["MABPR_MPRMO_TEXT"]; ?>" class="form-control" id="model" />
                        </div>
                        <div class="col-lg-6">
                            <label>Version:</label>
                            <input disabled type="text" value="<?php echo $selected_batch_production["MABPR_MPRVE_TEXT"]; ?>" class="form-control" id="version" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>3. Sub Batch Delivery Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Sub Batch Name:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $data["SUBPA_TEXT"]; ?>">
                        </div>
                        <div class="col-lg-4">
                            <label>Packaging Center:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $data["SUBPA_MAPLA_TEXT"]; ?>">
                        </div>
                        <div class="col-lg-4">
                            <label>Sub Batch Quantity:</label>
                            <input disabled type="text" class="form-control numeric_input" value="<?php echo $data["SUBPA_QTY"]; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Scheduled Sub Batch Date:</label>
                            <input disabled type="text" class="form-control" value="<?php echo substr($data["SUBPA_START_TIMESTAMP"],0,10) ?>">
                        </div>
                        <div class="col-lg-3">
                            <label>Scheduled Batch Start Time:</label>
                            <input class="form-control" disabled id="SUBPA_TIME_START" name="SUBPA_TIME_START" placeholder="Select time" value="<?php echo substr($data["SUBPA_START_TIMESTAMP"],11,5) ?>" type="text"/>
                        </div>
                        <!-- <div class="col-lg-3">
                            <label>Batch End Date:</label>
                            <input type="text" class="form-control" data-date-format="yyyy-mm-dd" name="SUBPA_DATE_END" id="SUBPA_DATE_END" placeholder="Select date" />
                        </div> -->
                        <div class="col-lg-3">
                            <label>Scheduled Batch End Time:</label>
                            <input class="form-control" disabled id="SUBPA_TIME_END" name="SUBPA_TIME_END" placeholder="Select time" value="<?php echo substr($data["SUBPA_END_TIMESTAMP"],11,5) ?>" type="text"/>
                        </div>
                        <div class="col-lg-3">
                            <label>Paired Delivery QR:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $data["SUBPA_PAIRED_QTY"]; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Sub Batch Delivery Notes:</label>
                            <textarea <?php if ($data["SUBPA_ACTIVATION_STATUS"] == 2) { echo("disabled");} ?> rows="5" class="form-control" name="SUBPA_NOTES"><?php echo $data["SUBPA_NOTES"] ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>4. Assigned User Delivery</h4>
                        </div>
                    </div>
                    <?php for ($i=0; $i < count($staff); $i++) { ?>
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <label>User:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $staff[$i]["STBPA_EMP_TEXT"]; ?>">
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="card-footer">
                    <a href="{{ route('master_data_batch_delivery_view') }}" class="btn btn-secondary float-left">Back</a>
                    @if($data["SUBPA_ACTIVATION_STATUS"] == 0)
                        <button type="button" data-action="{{ route('master_data_batch_delivery_activate') }}" class="btn btn-primary float-right mr-2 activation_btn"> <i class="la la-check"></i>Activate batch</button>
                    @elseif($data["SUBPA_ACTIVATION_STATUS"] == 1)
                        <button type="button" data-action="{{ route('master_data_batch_delivery_close') }}" class="btn btn-primary float-right mr-2 close_btn"> <i class="la la-times"></i>Close batch</button>
                    @endif
                    <br><br>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
