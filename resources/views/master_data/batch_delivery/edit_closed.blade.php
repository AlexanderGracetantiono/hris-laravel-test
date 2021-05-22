@extends('layouts.app')

@section('page_title', 'Master Batch Delivery')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_delivery/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit  Delivery</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route('master_data_batch_delivery_update_progress') }}" data-form-success-redirect="{{ route("master_data_batch_delivery_view") }}" enctype="multipart/form-data">
                <div class="card-body">
                    @csrf
                    <input type="hidden" name="SUBPA_CODE" value="<?php echo $data["SUBPA_CODE"]; ?>">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Batch Acceptance Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Batch Acceptance:</label>
                            <input type="hidden" value="<?php echo($selected_batch_packaging["MABPA_CODE"]) ?>" name="SUBPA_MABPA_CODE">
                            <input disabled type="text" class="form-control" value="<?php echo($selected_batch_packaging["MABPA_TEXT"]) ?>">
                        </div>
                        <div class="col-lg-6">
                            <label>Remain Batch Quantity:</label>
                            <input readonly type="text" id="packaging_qty" value="<?php echo($selected_batch_packaging["MABPA_QTY_LEFT"]) ?>" class="form-control numeric_input">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Production Notes:</label>
                            <textarea disabled rows="5" class="form-control" id="batch_production_notes"><?php echo $selected_batch_production["MABPR_NOTES"]; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Acceptance Notes:</label>
                            <textarea disabled rows="5" class="form-control" id="batch_packaging_notes"><?php echo $selected_batch_packaging["MABPA_NOTES"]; ?></textarea>
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
                            <br><h4>3.  Delivery</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Batch Delivery Name:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $data["SUBPA_TEXT"]; ?>">
                        </div>
                        <div class="col-lg-4">
                            <label>New Batch Delivery Quantity:</label>
                            <input type="text" class="form-control numeric_input" name="SUBPA_QTY" value="<?php echo $data["SUBPA_QTY"]; ?>">
                        </div>
                        <div class="col-lg-4">
                            <label>Paired Delivery QR:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $data["SUBPA_PAIRED_QTY"]; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Scheduled  Start Date:</label>
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
                        <div class="col-lg-12">
                            <label>Batch Delivery Notes:</label>
                            <textarea disabled rows="5" class="form-control" id="batch_packaging_notes"><?php echo $data["SUBPA_NOTES"]; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>4. Assigned Staff Delivery</h4>
                        </div>
                    </div>
                    <?php for ($i=0; $i < count($staff); $i++) { ?>
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <label>Employee:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $staff[$i]["STBPA_EMP_TEXT"]; ?>">
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route('master_data_batch_delivery_view') }}" class="btn btn-secondary float-left">Back</a>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button type="button" id="submit_btn" class="btn float-right btn-primary mr-2">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
