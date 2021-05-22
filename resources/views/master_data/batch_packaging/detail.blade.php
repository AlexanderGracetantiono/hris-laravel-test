@extends('layouts.app')

@section('page_title', 'Batch Packaging ')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_packaging/activation_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Detail Batch Acceptance</h3>
                <div class="card-toolbar">
                    @if($data["MABPA_ACTIVATION_STATUS"] == 1)
                        <!-- <button type="button" data-action="{{ route('master_data_batch_packaging_close') }}" class="btn btn-success btn-pill float-right mr-2 close_btn"> <i class="la la-times"></i>Close batch</button> -->
                    @endif
                </div>
            </div>
            <form id="form_close" method="POST" data-form-success-redirect="{{ route('master_data_batch_packaging_view') }}">
                @csrf
                <input type="hidden" name="MABPA_CODE" value="{{ $data['MABPA_CODE'] }}">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Batch Production Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Batch Production:</label>
                            <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_TEXT"]) ?>" id="brand_product" class="form-control">
                        </div>
                        <div class="col-lg-3">
                            <label>Production Center:</label>
                            <input disabled type="text" value="<?php echo($data["MABPA_MAPLA_TEXT"]) ?>" id="paired_quantity_product" class="form-control">
                        </div>
                        <div class="col-lg-3">
                            <label>Brand Product:</label>
                            <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MBRAN_TEXT"]) ?>" id="brand_product" class="form-control">
                        </div>
                        <div class="col-lg-3">
                            <label>Production Paired Quantity:</label>
                            <input disabled type="text" value="<?php echo($data["MABPA_QTY"]) ?>" id="paired_quantity_product" class="form-control">
                        </div>
                        
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Production Notes:</label>
                            <textarea disabled class="form-control" name="MABPA_NOTES" rows="3"><?php echo($selected_batch_production["MABPR_NOTES"]) ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>2. Production Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Category:</label>
                            <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MPRCA_TEXT"]) ?>" id="category_product" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label>Product:</label>
                            <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MPRDT_TEXT"]) ?>" id="product" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Model:</label>
                            <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MPRMO_TEXT"]) ?>" id="model_product" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label>Version:</label>
                            <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MPRVE_TEXT"]) ?>" id="version_product" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>SKU:</label>
                            <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MPRVE_SKU"]) ?>" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label>Product Description:</label>
                            <textarea disabled type="text" rows="5" class="form-control"><?php echo($selected_batch_production["MABPR_MPRVE_NOTES"]) ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <br><h4>3. Batch Acceptance Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Batch Acceptance Activate Timestamp:</label>
                            <input disabled type="text" value="<?php echo($data["MABPA_ACTIVATION_TIMESTAMP"]) ?>" class="form-control">
                        </div>
                        <div class="col-lg-4">
                            <label>Batch Acceptance Close Timestamp:</label>
                            <input disabled type="text" value="<?php echo($data["MABPA_CLOSED_TIMESTAMP"]) ?>" class="form-control">
                        </div>
                        <div class="col-lg-4">
                            <label>Packaging Paired Quantity:</label>
                            <input disabled type="text" value="<?php echo($data["MABPA_PAIRED_QTY"]) ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Acceptance Notes:</label>
                            <textarea disabled class="form-control" name="MABPA_NOTES" rows="3"><?php echo($data["MABPA_NOTES"]) ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route("master_data_batch_packaging_view") }}" class="btn btn-secondary">Back</a>
                    
                    <br>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
