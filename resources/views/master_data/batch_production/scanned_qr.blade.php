@extends('layouts.app')

@section('page_title', 'Master Batch Production')

@push('styles')
<style>
    #image_qr {
        opacity: 0px;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_production/activation_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/show_hide_qr.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_production/report.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Close Batch Production</h3>
                <div class="card-toolbar">
                    <button type="button" data-action="{{ route('master_data_batch_production_close') }}" class="btn btn-success btn-pill float-right mr-2 close_btn"> <i class="la la-times"></i>Close batch</button>
                </div>
            </div>
            <div class="card-body">
                <form class="form" id="form_close" method="POST" data-form-success-redirect="{{ route('master_data_batch_production_view') }}">
                    @csrf
                    <input type="hidden" name="MABPR_CODE" value="{{ $data_production['MABPR_CODE'] }}">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Batch Production Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Packaging Center:</label>
                            <select class="form-control select2" id="plant_packaging_select2" name="MABPA_MAPLA_CODE">
                                <option></option>
                                <?php for ($j = 0; $j < count($plant_packaging); $j++) { ?>
                                    <option value="<?php echo $plant_packaging[$j]["MAPLA_CODE"] ?>"><?php echo $plant_packaging[$j]["MAPLA_TEXT"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label>Targeted Quantity:</label>
                            <input disabled id="target_qty" type="text" class="form-control" value="<?php echo $data_production["MABPR_EXPECTED_QTY"]; ?>">
                        </div>
                        <div class="col-lg-2">
                            <label>Paired:</label>
                            <input disabled id="paired_qr" type="text" class="form-control" value="<?php echo $count_paired_qr; ?>">
                        </div>
                        <div class="col-lg-2">
                            <label>Not Paired:</label>
                            <input id="unpaired" readonly type="text" class="form-control numeric_input" value="0">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Production Notes:</label>
                            <textarea <?php if ($data_production["MABPR_ACTIVATION_STATUS"] == 2) { echo("disabled");} ?> rows="5" class="form-control" name="MABPR_NOTES"><?php echo $data_production["MABPR_NOTES"] ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>2. Closing Batch Production Report</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-2">
                            <label>Discrepancy Product:</label>
                            <input id="MABPR_DISCREPANCY_PRODUCT" type="text" class="form-control numeric_input" value="{{ $data_production['MABPR_DISCREPANCY_PRODUCT'] }}" name="MABPR_DISCREPANCY_PRODUCT">
                        </div>
                        <div class="col-lg-2">
                            <label>Returned Product:</label>
                            <input id="MABPR_RETURNED_PRODUCT" readonly type="text" class="form-control numeric_input">
                        </div>
                        <div class="col-lg-2">
                            <label>Discrepancy QR Alpha:</label>
                            <input id="MABPR_DISCREPANCY_TRQRA" type="text" class="form-control numeric_input" value="{{ $data_production['MABPR_DISCREPANCY_TRQRA'] }}" name="MABPR_DISCREPANCY_TRQRA">
                        </div>
                        <div class="col-lg-2">
                            <label>Returned QR Alpha:</label>
                            <input id="MABPR_RETURNED_TRQRA" readonly type="text" class="form-control numeric_input">
                        </div>
                        <div class="col-lg-2">
                            <label>Discrepancy QR Bridge:</label>
                            <input id="MABPR_DISCREPANCY_MASCO" type="text" class="form-control numeric_input" value="{{ $data_production['MABPR_DISCREPANCY_MASCO'] }}" name="MABPR_DISCREPANCY_MASCO">
                        </div>
                        <div class="col-lg-2">
                            <label>Returned QR Bridge:</label>
                            <input id="MABPR_RETURNED_MASCO" readonly type="text" class="form-control numeric_input">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Closing Batch Production Notes:</label>
                            <textarea rows="5" class="form-control" name="MABPR_DISCREPANCY_NOTES">{{ $data_production['MABPR_DISCREPANCY_NOTES'] }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Detail Batch Production</h3>
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
                                <label>Production Center:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MAPLA_TEXT"]; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label>Batch Target Quantity:</label>
                                <input disabled id="target_qty" type="text" class="form-control" value="<?php echo $data_production["MABPR_EXPECTED_QTY"]; ?>">
                            </div>
                            <div class="col-lg-6">
                                <label>Paired Production QR:</label>
                                <input disabled id="paired_qr" type="text" class="form-control" value="<?php echo $count_paired_qr; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label>Scheduled Batch Date:</label>
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
                                <label>Actual Batch Date:</label>
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
                    </div>
                    <div class="col-lg-4">
                        <label>Batch Production QR:</label><br>
                        <div id="image_qr">
                            <?php echo (QrCode::format('svg')->size(240)->generate($link_qr)) ?>
                        </div>
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
                        <label>Category:</label>
                        <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MPRCA_TEXT"]; ?>">
                    </div>
                    <div class="col-lg-6">
                        <label>Product:</label>
                        <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MPRDT_TEXT"]; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Model:</label>
                        <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MPRMO_TEXT"]; ?>">
                    </div>
                    <div class="col-lg-6">
                        <label>Version:</label>
                        <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MPRVE_TEXT"]; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>SKU:</label>
                        <input disabled type="text" class="form-control" value="<?php echo $data_production["MABPR_MPRVE_SKU"]; ?>">
                    </div>
                    <div class="col-lg-6">
                        <label>Product Description:</label>
                        <textarea disabled rows="5" class="form-control">{{ $data_production["MABPR_MPRVE_NOTES"] }}</textarea>
                    </div>
                </div>
                <div id="qr_list">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>3. Paired QR List</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-checkable" id="kt_datatable1">
                                <thead>
                                    <tr>
                                        <th>QR Bridge</th>
                                        <th>Scanned By</th>
                                        <th>Scanned Timestamp</th>
                                        <th data-priority="1" width="20px">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_qr as $row)
                                    <tr>
                                        <td>{{ $row["TRQRA_MASCO_CODE"] }}</td>
                                        <td>{{ $row["TRQRA_EMP_SCAN_TEXT"] }}</td>
                                        <td>{{ $row["TRQRA_EMP_SCAN_TIMESTAMP"] }}</td>
                                        <td>
                                            @if($row["TRQRA_STATUS"] == 1)
                                                Paired
                                            @else
                                                Rejected
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6">
                        <a href="{{ route("master_data_batch_production_view") }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
