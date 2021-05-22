@extends('layouts.app')

@section('page_title', 'Master Batch Packaging')

@push('styles')
<style>
    #image_qr {
        opacity: 0px;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/sub_batch_packaging/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/sub_batch_packaging/show_hide_qr.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Scanned QR Batch Packaging</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <br><h4>1. Batch Packaging Data</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label>Batch Name:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $data_sub_batch["SUBPA_TEXT"]; ?>">
                            </div>
                            <div class="col-lg-6">
                                <label>Packaging Center:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $data_sub_batch["SUBPA_MAPLA_TEXT"]; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label>Batch Target Quantity:</label>
                                <input disabled type="text" class="form-control numeric_input" value="<?php echo $data_sub_batch["SUBPA_QTY"]; ?>">
                            </div>
                            <div class="col-lg-4">
                                <label>Paired:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $count_paired_qr; ?>">
                            </div>
                            <div class="col-lg-4">
                                <label>Not Paired:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $data_sub_batch["SUBPA_QTY"] - $count_paired_qr; ?>">
                            </div>
                            <!-- <div class="col-lg-3">
                                <label>Store Admin:</label>
                                <input disabled type="text" class="form-control" value="<?php echo $data_sub_batch["SUBPA_STORE_ADMIN_TEXT"]; ?>">
                            </div> -->
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <label>Scheduled Batch Date:</label>
                                <input disabled type="text" class="form-control" value="<?php echo substr($data_sub_batch["SUBPA_START_TIMESTAMP"],0,10) ?>">
                            </div>
                            <div class="col-lg-4">
                                <label>Scheduled Batch Start Time:</label>
                                <input class="form-control" disabled id="SUBPA_TIME_START" name="SUBPA_TIME_START" placeholder="Select time" value="<?php echo substr($data_sub_batch["SUBPA_START_TIMESTAMP"],11,5) ?>" type="text"/>
                            </div>
                            <div class="col-lg-4">
                                <label>Scheduled Batch End Time:</label>
                                <input class="form-control" disabled id="SUBPA_TIME_END" name="SUBPA_TIME_END" placeholder="Select time" value="<?php echo substr($data_sub_batch["SUBPA_END_TIMESTAMP"],11,5) ?>" type="text"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label>Actual Batch Start Date:</label>
                                <input disabled type="text" class="form-control" value="<?php if ($data_qr != null) { echo substr($data_qr[0]["MASCO_UPDATED_TIMESTAMP"],0,10); } ?>">
                            </div>
                            <div class="col-lg-3">
                                <label>Actual Batch Start Time:</label>
                                <input disabled type="text" class="form-control" value="<?php if ($data_qr != null) { echo substr($data_qr[0]["MASCO_UPDATED_TIMESTAMP"],11,5); } ?>">
                            </div>
                            <div class="col-lg-3">
                                <label>Actual Batch End Time:</label>
                                <input disabled type="text" class="form-control" value="<?php echo substr($data_sub_batch["SUBPA_CLOSED_TIMESTAMP"],0,10); ?>">
                            </div>
                            <div class="col-lg-3">
                                <label>Actual Batch End Time:</label>
                                <input disabled type="text" class="form-control" value="<?php echo substr($data_sub_batch["SUBPA_CLOSED_TIMESTAMP"],11,5); ?>">
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-lg-4">
                        <label>Batch Packaging QR:</label><br>
                        <div id="image_qr">
                            <?php echo (QrCode::format('svg')->size(240)->generate($link_qr)) ?>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Batch Packaging Notes:</label>
                        <textarea disabled rows="5" class="form-control"><?php echo $data_sub_batch["SUBPA_NOTES"] ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <br><h4>2. Closing Batch Packaging Report</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-2">
                        <label>Discrepancy Product:</label>
                        <input disabled class="form-control" value="<?php echo($data_sub_batch["SUBPA_DISCREPANCY_PRODUCT"]) ?>" type="text"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Returned Product:</label>
                        <input disabled class="form-control" value="<?php echo($data_sub_batch["SUBPA_RETURNED_PRODUCT"]) ?>" type="text"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Discrepancy QR Bridge:</label>
                        <input disabled class="form-control" value="<?php echo($data_sub_batch["SUBPA_DISCREPANCY_MASCO"]) ?>" type="text"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Returned QR Bridge:</label>
                        <input disabled class="form-control" value="<?php echo($data_sub_batch["SUBPA_RETURNED_MASCO"]) ?>" type="text"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Discrepancy QR Zeta:</label>
                        <input disabled class="form-control" value="<?php echo($data_sub_batch["SUBPA_DISCREPANCY_TRQRZ"]) ?>" type="text"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Returned QR Zeta:</label>
                        <input disabled class="form-control" value="<?php echo($data_sub_batch["SUBPA_RETURNED_TRQRZ"]) ?>" type="text"/>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Closing Batch Packaging Notes:</label>
                        <textarea disabled rows="5" class="form-control"><?php echo $data_sub_batch["SUBPA_DISCREPANCY_NOTES"] ?></textarea>
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
                        <input disabled type="text" value="<?php echo $pool_product["POPRD_MPRCA_TEXT"]; ?>" class="form-control" id="category" />
                    </div>
                    <div class="col-lg-6">
                        <label>Product:</label>
                        <input disabled type="text" value="<?php echo $pool_product["POPRD_MPRDT_TEXT"]; ?>" class="form-control" id="product" />
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Model:</label>
                        <input disabled type="text" value="<?php echo $pool_product["POPRD_MPRMO_TEXT"]; ?>" class="form-control" id="model" />
                    </div>
                    <div class="col-lg-6">
                        <label>Version:</label>
                        <input disabled type="text" value="<?php echo $pool_product["POPRD_MPRVE_TEXT"]; ?>" class="form-control" id="version" />
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>SKU:</label>
                        <input disabled type="text" value="<?php echo $pool_product["POPRD_MPRVE_SKU"]; ?>" class="form-control" />
                    </div>
                    <div class="col-lg-6">
                        <label>Product Description:</label>
                        <textarea disabled rows="5" value="<?php echo $pool_product["POPRD_MPRVE_TEXT"]; ?>" class="form-control"><?php echo $pool_product["POPRD_MPRVE_NOTES"]; ?></textarea>
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
                                        <th>QR Zeta</th>
                                        <th>Scanned By</th>
                                        <th>Scanned Timestamp</th>
                                        <th data-priority="1" width="20px">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_qr as $row)
                                    <tr>
                                        <td>{{ $row["MASCO_TRQZH_CODE"] }}</td>
                                        <td>{{ $row["MASCO_UPDATED_TEXT"] }}</td>
                                        <td>{{ $row["MASCO_UPDATED_TIMESTAMP"] }}</td>
                                        <td>
                                            @if ($row["MASCO_STATUS"] == 1)
                                                Paired
                                            @elseif ($row["MASCO_STATUS"] == 2)
                                                Reported
                                            @elseif ($row["MASCO_STATUS"] == 3)
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
                        <a href="{{ route("master_data_sub_batch_packaging_view") }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
