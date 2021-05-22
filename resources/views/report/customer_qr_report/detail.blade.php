@extends('layouts.app')

@section('page_title', 'Detail Customer Report')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product_version/datatable.js?=1') }}"></script>
<!-- <script src="{{ asset('custom/js/master_data/batch_store/activation_ajax.js') }}"></script> -->
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Detail Customer Report</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <h4>1. Customer Data</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-4">
                        <label>Customer Name:</label>
                        <input disabled type="text" value="{{ $data_customer['REPQR_CST_SCAN_TEXT'] }}" class="form-control">
                    </div>
                    <div class="col-lg-4">
                        <label>Customer Email:</label>
                        <input disabled type="text" value="{{ $data_customer['REPQR_CST_SCAN_EMAIL'] }}" class="form-control">
                    </div>
                    <div class="col-lg-4">
                        <label>Customer Phone Number:</label>
                        <input disabled type="text" value="{{ $data_customer['REPQR_CST_SCAN_PHONE_NUMBER'] }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Customer Notes:</label>
                        <textarea disabled class="form-control">{{ $data_customer['REPQR_CST_SCAN_NOTES'] }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <br><h4>2. QR Alpha & Zeta</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>QR Alpha:</label>
                        <input disabled type="text" value="{{ $data_customer['REPQR_TRQRA'] }}" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label>QR Zeta:</label>
                        <input disabled type="text" value="{{ $data_customer['REPQR_TRQRZ'] }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <br><h4>3. Product Data</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Category:</label>
                        <input disabled type="text" value="<?php echo $data_product["SCDET_MPRCA_TEXT"] ?>" id="category_product" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label>Product:</label>
                        <input disabled type="text" value="<?php echo $data_product["SCDET_MPRDT_TEXT"] ?>" id="product" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Model:</label>
                        <input disabled type="text" value="<?php echo $data_product["SCDET_MPRMO_TEXT"] ?>" id="model_product" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label>Version:</label>
                        <input disabled type="text" value="<?php echo $data_product["SCDET_MPRVE_TEXT"] ?>" id="version_product" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>SKU:</label>
                        <input disabled type="text" value="<?php echo $data_product["SCDET_MPRVE_SKU"] ?>" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label>Product Description:</label>
                        <textarea disabled rows="5" type="text" class="form-control"><?php echo $data_product["SCDET_MPRVE_NOTES"] ?></textarea>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <br><h4>4. Log Scan</h4>
                    </div>
                </div>
                <table class="table table-bordered table-checkable" id="kt_datatable1">
                    <thead>
                        <tr>
                            <tr>
                                <th>Order</th>
                                <th>User Name</th>
                                <th>Location</th>
                                <th>Timestamp</th>
                            </tr>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i=0; $i < count($log_scan); $i++) { ?>
                            <tr>
                                <td>{{count($log_scan) - $i }}</td>
                                <td>{{ $log_scan[$i]["SCLOG_CST_SCAN_TEXT"] }}</td>
                                <td>{{ $log_scan[$i]["SCLOG_CST_SCAN_LAT"] }}</td>
                                <td>{{ $log_scan[$i]["SCLOG_CST_SCAN_TIMESTAMP"] }}</td>
                               
                            </tr>
                            <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('customer_report_qr_view') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
        
    </div>
</div>
@endsection
