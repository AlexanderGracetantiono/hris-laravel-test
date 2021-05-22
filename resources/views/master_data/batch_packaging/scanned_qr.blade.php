@extends('layouts.app')

@section('page_title', 'Batch Packaging ')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_packaging/activation_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_packaging/datatable.js?=1') }}"></script>
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
                        <br><h4>Batch Info</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-3">
                        <label>Batch Packaging Name:</label>
                        <input disabled type="text" value="<?php echo($data_packaging["MABPA_TEXT"]) ?>" class="form-control" name="MABPA_TEXT" placeholder="Input batch name">
                    </div>
                    <div class="col-lg-3">
                        <label>Batch Acceptance Date:</label>
                        <input disabled type="text" value="<?php echo($data_packaging["MABPA_DATE"]) ?>" class="form-control" data-date-format="yyyy-mm-dd" name="MABPA_DATE" id="MABPA_DATE" placeholder="Select date"/>
                    </div>
                    <!-- <div class="col-lg-3">
                        <label>Batch Packaging Quantity:</label>
                        <input disabled type="text" value="<?php /* echo($data_packaging["MABPA_QTY"])*/ ?>" class="form-control numeric_input" name="MABPA_QTY" placeholder="Input quantity batch">
                    </div> -->
                    <div class="col-lg-3">
                        <label>Paired QR Packaging:</label>
                        <input disabled type="text" value="<?php echo($data_packaging["MABPA_PAIRED_QTY"]) ?>" class="form-control" name="MABPA_TEXT">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-3">
                        <label>Batch Production:</label>
                        <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_TEXT"]) ?>" id="brand_product" class="form-control">
                    </div>
                    <div class="col-lg-3">
                        <label>Brand Product:</label>
                        <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MBRAN_TEXT"]) ?>" id="brand_product" class="form-control">
                    </div>
                    <div class="col-lg-3">
                        <label>Category Product:</label>
                        <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MPRCA_TEXT"]) ?>" id="category_product" class="form-control">
                    </div>
                    <div class="col-lg-3">
                        <label>Product:</label>
                        <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MPRDT_TEXT"]) ?>" id="product" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-3">
                        <label>Model Product:</label>
                        <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MPRMO_TEXT"]) ?>" id="model_product" class="form-control">
                    </div>
                    <div class="col-lg-3">
                        <label>Version Product:</label>
                        <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MPRVE_TEXT"]) ?>" id="version_product" class="form-control">
                    </div>
                    <div class="col-lg-3">
                        <label>Production Paired Quantity:</label>
                        <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_PAIRED_QTY"]) ?>" id="paired_quantity_product" class="form-control">
                    </div>
                    <div class="col-lg-3">
                        <label>Plant:</label>
                        <input disabled type="text" value="<?php echo($data_packaging["MABPA_MAPLA_TEXT"]) ?>" id="paired_quantity_product" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Notes:</label>
                        <textarea disabled class="form-control" name="MABPA_NOTES" rows="3"><?php echo($data_packaging["MABPA_NOTES"]) ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <br><h4>2. Paired QR List</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable1">
                            <thead>
                                <tr>
                                    <th>Sub Batch Packaging</th>
                                    <th>Sticker Code</th>
                                    <th>Notes</th>
                                    <th>Status</th>
                                    <th>Scanned By</th>
                                    <th>Scanned Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_qr as $row)
                                <tr>
                                    <td>{{ $row["MASCO_SUBPA_TEXT"] }}</td>
                                    <td>{{ $row["MASCO_CODE"] }}</td>
                                    <td>{{ $row["MASCO_NOTES"] }}</td>
                                    <td>
                                        @if ($row["MASCO_STATUS"] == 1)
                                            Paired
                                        @elseif ($row["MASCO_STATUS"] == 2)
                                            Reported
                                        @elseif ($row["MASCO_STATUS"] == 3)
                                            Rejected
                                        @endif
                                    </td>
                                    <td>{{ $row["MASCO_UPDATED_TEXT"] }}</td>
                                    <td>{{ $row["MASCO_UPDATED_TIMESTAMP"] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
