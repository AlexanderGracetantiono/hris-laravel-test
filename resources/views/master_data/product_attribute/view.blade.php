@extends('layouts.app')

@section('page_title', 'View Product Attribute')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product_attribute/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/product_attribute/repeater.js') }}"></script>
<script src="{{ asset('custom/js/master_data/product_attribute/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/product_attribute/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <h3 class="card-label">Detail Product</h3>
        </div>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Category:</label>
                <input type="text" class="form-control" value="{{ $data['MPRVE_MPRCA_TEXT'] }}" disabled />
            </div>
            <div class="col-lg-4">
                <label>Product:</label>
                <input type="text" class="form-control" value="{{ $data['MPRVE_MPRDT_TEXT'] }}" disabled />
            </div>
            <div class="col-lg-4">
                <label>Model:</label>
                <input type="text" class="form-control" value="{{ $data['MPRVE_MPRMO_TEXT'] }}" disabled />
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Version Name:</label>
                <input type="text" class="form-control" value="{{ $data['MPRVE_TEXT'] }}" disabled />
            </div>
            <div class="col-lg-4">
                <label>SKU:</label>
                <input type="text" class="form-control" value="{{ $data['MPRVE_SKU'] }}" disabled />
            </div>
            <div class="col-lg-4">
                <label>Description:</label>
                <input type="text" class="form-control" value="{{ $data['MPRVE_NOTES'] }}" disabled />
            </div>
        </div>
        </div>
        </div>
        <br>
<div class="card card-custom">
    
        <div class="card-header py-3">
            <div class="card-title">
                <h3 class="card-label">Attribute General</h3>
            </div>
        </div>
    <form class="form" id="form_general" method="POST" action="{{ route('product_attribute_update_general') }}" data-form-success-redirect="{{ route('product_attribute_view', ['code' => $data_general[0]['TRPAT_KEY_CODE'], 'level' => $data_general[0]['TRPAT_KEY_TYPE']]) }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            
            @foreach($data_general as $general)
                <input type="hidden" value="{{ $general['TRPAT_ID'] }}" name="general_id[]">
                <div class="form-group row">
                    <div class="col-lg-5">
                        <label>Default Label:</label>
                        <input type="hidden" value="{{ $general['TRPAT_LABEL'] }}" name="general_label[]">
                        <input type="text" disabled class="form-control" value="{{ $general['TRPAT_LABEL'] }}">
                    </div>
                    <div class="col-lg-5">
                        <label>Masking Label:</label>
                        <input type="text" class="form-control" value="{{ $general['TRPAT_MASKING'] }}" name="general_masking[]" placeholder="Input masking">
                    </div>
                    <div class="col-lg-2">
                        <label>Activation Status:</label>
                        <select class="form-control select2 activation_status" name="general_activation[]">
                            <option></option>
                            <option @if($general['TRPAT_ACTIVE_STATUS'] == 1) selected @endif value="1">Active</option>
                            <option @if($general['TRPAT_ACTIVE_STATUS'] == 2) selected @endif value="2">In-active</option>
                        </select>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="button" id="submit_general_btn" class="btn btn-primary mr-2">Save General</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection