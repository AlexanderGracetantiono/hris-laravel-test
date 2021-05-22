@extends('layouts.detail_note')

@section('page_title', 'Batch Production')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_production/activation_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/datatable.js?=1') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Scanned QR Batch Production</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <h4>1. Batch Production Data</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label>Batch Name:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_TEXT"]; ?>">
                            </div>
                            <div class="col-lg-6">
                                <label>Plant:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MAPLA_TEXT"]; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label>Expected Quantity:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_EXPECTED_QTY"]; ?>">
                            </div>
                            <div class="col-lg-6">
                                <label>Paired Production QR:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $count_paired_qr; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label>Scheduled Batch Start Date:</label>
                                <input disabled type="text" class="form-control" value="<?php echo substr($data_production["MABPR_START_TIMESTAMP"],0,10); ?>">
                            </div>
                            <div class="col-lg-4">
                                <label>Scheduled Batch Start Time:</label>
                                <input disabled class="form-control" id="MABPR_TIME_START" name="MABPR_TIME_START" placeholder="Select time" value="<?php echo substr($data_production["MABPR_START_TIMESTAMP"],11,5) ?>" type="text"/>
                            </div>
                            <!-- <div class="col-lg-4">
                                <label>Batch End Date:</label>
                                <input type="text" class="form-control" data-date-format="yyyy-mm-dd" name="MABPR_DATE_END" id="MABPR_DATE_END" placeholder="Select date" />
                            </div> -->
                            <div class="col-lg-4">
                                <label>Scheduled Batch End Time:</label>
                                <input disabled class="form-control" id="MABPR_TIME_END" name="MABPR_TIME_END" placeholder="Select time" value="<?php echo substr($data_production["MABPR_END_TIMESTAMP"],11,5) ?>" type="text"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label>Actual Batch Start Date:</label>
                                <input disabled class="form-control" value="<?php if ($data_qr != null) { echo substr($data_qr[0]["TRQRA_EMP_SCAN_TIMESTAMP"],0,10); } ?>" type="text"/>
                            </div>
                            <div class="col-lg-3">
                                <label>Actual Batch Start Time:</label>
                                <input disabled class="form-control" value="<?php if ($data_qr != null) { echo substr($data_qr[0]["TRQRA_EMP_SCAN_TIMESTAMP"],11,5); } ?>" type="text"/>
                            </div>
                            <div class="col-lg-3">
                                <label>Actual Batch End Date:</label>
                                <input disabled class="form-control" value="<?php echo substr($data_production["MABPR_CLOSED_TIMESTAMP"],0,10); ?>" type="text"/>
                            </div>
                            <div class="col-lg-3">
                                <label>Actual Batch End Time:</label>
                                <input disabled class="form-control" value="<?php echo substr($data_production["MABPR_CLOSED_TIMESTAMP"],11,5); ?>" type="text"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label>Returned QR Alpha:</label>
                                <input disabled class="form-control" id="MABPR_TIME_START" name="MABPR_TIME_START" value="<?php echo($data_production["MABPR_RETURNED_TRQRA"]) ?>" type="text"/>
                            </div>
                            <div class="col-lg-3">
                                <label>Returned Sticker Code:</label>
                                <input disabled class="form-control" id="MABPR_TIME_END" name="MABPR_TIME_END" value="<?php echo($data_production["MABPR_RETURNED_MASCO"]) ?>" type="text"/>
                            </div>
                            <div class="col-lg-3">
                                <label>Discrepancy QR Alpha:</label>
                                <input disabled class="form-control" id="MABPR_TIME_START" name="MABPR_TIME_START" value="<?php echo($data_production["MABPR_DISCREPANCY_TRQRA"]) ?>" type="text"/>
                            </div>
                            <div class="col-lg-3">
                                <label>Discrepancy Sticker Code:</label>
                                <input disabled class="form-control" id="MABPR_TIME_END" name="MABPR_TIME_END" value="<?php echo($data_production["MABPR_DISCREPANCY_MASCO"]) ?>" type="text"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label>Batch Production QR:</label><br>
                        <div id="image_qr">
                            <?php echo (QrCode::format('svg')->size(240)->generate($link_qr)) ?>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Batch Production Notes:</label>
                        <textarea disabled rows="5" class="form-control"><?php echo $data_production["MABPR_NOTES"] ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Batch Production Discrepancy Notes:</label>
                        <textarea disabled rows="5" class="form-control"><?php echo $data_production["MABPR_DISCREPANCY_NOTES"] ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <br>
                        <h4>2. Product Data</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Product Category:</label>
                        <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MPRCA_TEXT"]; ?>">
                    </div>
                    <div class="col-lg-6">
                        <label>Product:</label>
                        <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MPRDT_TEXT"]; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Product Model:</label>
                        <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MPRMO_TEXT"]; ?>">
                    </div>
                    <div class="col-lg-6">
                        <label>Product Version:</label>
                        <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MPRVE_TEXT"]; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Product SKU:</label>
                        <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MPRVE_SKU"]; ?>">
                    </div>
                    <div class="col-lg-6">
                        <label>Product Description:</label>
                        <textarea disabled rows="5" class="form-control">{{ $data_production["MABPR_MPRVE_NOTES"] }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
