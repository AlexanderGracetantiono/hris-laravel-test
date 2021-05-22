@extends('layouts.app')

@section('page_title', 'Batch Acceptance ')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_packaging/batch_production.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_packaging/date.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_packaging/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_packaging/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_packaging/activation_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Accept Batch Acceptance</h3>
                <div class="card-toolbar">
                    <button type="button" data-action="{{ route('master_data_batch_packaging_activate') }}" class="btn btn-success btn-pill float-right mr-2 activation_btn"> <i class="la la-check"></i>Accept batch</button>
                </div>
            </div>
            <div class="card-body">
                <form class="form" id="form_activation" method="POST" data-form-success-redirect="{{ route('master_data_batch_packaging_view') }}">
                    @csrf
                    <input type="hidden" name="MABPA_CODE" value="{{ $data['MABPA_CODE'] }}">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Acceptance Notes:</label>
                            <textarea class="form-control" name="MABPA_NOTES" rows="3"><?php echo($data["MABPA_NOTES"]) ?></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit Batch Acceptance Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route('master_data_batch_packaging_activate') }}" data-form-success-redirect="{{ route("master_data_batch_packaging_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MABPA_CODE" value="<?php echo $data["MABPA_CODE"]; ?>">
                <input type="hidden" name="OLD_MABPR_CODE" value="<?php echo $selected_batch_production["MABPR_CODE"]; ?>">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h4>1. Batch Production Data</h4>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Batch Production:</label>
                            <input type="hidden" value="<?php echo($selected_batch_production["MABPR_CODE"]) ?>" name="MABPA_MABPR_CODE">
                            <input disabled type="text" class="form-control" value="<?php echo($selected_batch_production["MABPR_TEXT"]) ?>">
                        </div>
                        <div class="col-lg-3">
                            <label>Brand Product:</label>
                            <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MBRAN_TEXT"]) ?>" id="brand_product" class="form-control">
                        </div>
                        <div class="col-lg-3">
                            <label>Production Paired Quantity:</label>
                            <input disabled type="text" value="<?php echo($data["MABPA_QTY"]) ?>" id="paired_quantity_product" class="form-control">
                        </div>
                        <div class="col-lg-3">
                            <label>Production Center:</label>
                            <input disabled type="text" value="<?php echo($selected_batch_production["MABPR_MAPLA_TEXT"]) ?>" id="paired_quantity_product" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Batch Production Notes:</label>
                            <textarea disabled class="form-control" rows="3"><?php echo($selected_batch_production["MABPR_NOTES"]) ?></textarea>
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
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_batch_packaging_view") }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
