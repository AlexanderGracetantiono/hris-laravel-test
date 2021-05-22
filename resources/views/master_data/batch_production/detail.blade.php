@extends('layouts.app')

@section('page_title', 'Master Batch Production')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_production/activation_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Detail Batch Production Form</h3>
            </div>
            <form class="form" id="form" method="POST" data-form-success-redirect="{{ route('master_data_batch_production_view') }}">
                @csrf
                <input type="hidden" name="MABPR_CODE" value="{{ $data['MABPR_CODE'] }}">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Batch Production Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Batch Name:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $data["MABPR_TEXT"]; ?>">
                        </div>
                        <div class="col-lg-3">
                            <label>Batch Quantity:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $data["MABPR_EXPECTED_QTY"]; ?>">
                        </div>
                        <div class="col-lg-3">
                            <label>Plant:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $data["MABPR_MAPLA_TEXT"]; ?>">
                        </div>
                        <div class="col-lg-3">
                            <label>Paired Production QR:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $count_paired_qr; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Scheduled Batch Start Date:</label>
                            <input disabled type="text" class="form-control" value="<?php echo substr($data["MABPR_START_TIMESTAMP"],0,10); ?>">
                        </div>
                        <div class="col-lg-4">
                            <label>Scheduled Batch Start Time:</label>
                            <input disabled class="form-control" id="MABPR_TIME_START" name="MABPR_TIME_START" placeholder="Select time" value="<?php echo substr($data["MABPR_START_TIMESTAMP"],11,5) ?>" type="text"/>
                        </div>
                        <!-- <div class="col-lg-4">
                            <label>Batch End Date:</label>
                            <input type="text" class="form-control" data-date-format="yyyy-mm-dd" name="MABPR_DATE_END" id="MABPR_DATE_END" placeholder="Select date" />
                        </div> -->
                        <div class="col-lg-4">
                            <label>Scheduled Batch End Time:</label>
                            <input disabled class="form-control" id="MABPR_TIME_END" name="MABPR_TIME_END" placeholder="Select time" value="<?php echo substr($data["MABPR_END_TIMESTAMP"],11,5) ?>" type="text"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Actual Batch Start Time:</label>
                            <input disabled class="form-control" id="MABPR_TIME_START" name="MABPR_TIME_START" value="<?php echo substr($data_qr["TRQRA_EMP_SCAN_TIMESTAMP"],11,5); ?>" type="text"/>
                        </div>
                        <!-- <div class="col-lg-6">
                            <label>Batch End Date:</label>
                            <input type="text" class="form-control" data-date-format="yyyy-mm-dd" name="MABPR_DATE_END" id="MABPR_DATE_END" placeholder="Select date" />
                        </div> -->
                        <div class="col-lg-6">
                            <label>Actual Batch End Time:</label>
                            <input disabled class="form-control" id="MABPR_TIME_END" name="MABPR_TIME_END" value="<?php echo substr($data["MABPR_ACTIVATION_TIMESTAMP"],11,5); ?>" type="text"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Production Notes:</label>
                            <textarea <?php if ($data["MABPR_ACTIVATION_STATUS"] == 2) { echo("disabled");} ?> rows="5" class="form-control" name="MABPR_NOTES"><?php echo $data["MABPR_NOTES"] ?></textarea>
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
                            <input disabled type="text" class="form-control" value="<?php echo $data["MABPR_MPRCA_TEXT"]; ?>">
                        </div>
                        <div class="col-lg-6">
                            <label>Product:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $data["MABPR_MPRDT_TEXT"]; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Product Model:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $data["MABPR_MPRMO_TEXT"]; ?>">
                        </div>
                        <div class="col-lg-6">
                            <label>Product Version:</label>
                            <input disabled type="text" class="form-control" value="<?php echo $data["MABPR_MPRVE_TEXT"]; ?>">
                        </div>
                       
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>3. Assigned User Production</h4>
                        </div>
                    </div>
                    <?php for ($i=0; $i < count($staff_production); $i++) { ?>
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <label>User:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $staff_production[$i]["STBPR_EMP_TEXT"]; ?>">
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="card-footer">
                    <a href="{{ route('master_data_batch_production_view') }}" class="btn btn-secondary float-left">Back</a>
                    @if($data["MABPR_ACTIVATION_STATUS"] == 0)
                        <button type="button" data-action="{{ route('master_data_batch_production_activate') }}" class="btn btn-primary float-right mr-2 activation_btn"> <i class="la la-check"></i>Activate batch</button>
                    @elseif($data["MABPR_ACTIVATION_STATUS"] == 1)
                        <button type="button" data-action="{{ route('master_data_batch_production_close') }}" class="btn btn-primary float-right mr-2 close_btn"> <i class="la la-times"></i>Close batch</button>
                    @endif
                    <br><br>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
