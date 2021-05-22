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
            <h3 class="card-label">Attribute General</h3>
        </div>
    </div>
    <form class="form" id="form_general" method="POST" action="{{ route('product_attribute_lab_update_general') }}" data-form-success-redirect="{{ route('product_attribute_lab_view', ['code' => $data_general[0]['TRPAT_KEY_CODE'], 'level' => $data_general[0]['TRPAT_KEY_TYPE']]) }}" enctype="multipart/form-data">
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
<br>
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <h3 class="card-label">Attribute Custom</h3>
        </div>
    </div>
    <form class="form" id="form_custom" method="POST" action="{{ route('product_attribute_lab_update_custom') }}" data-form-success-redirect="{{ route('product_attribute_lab_view', ['code' => $data_general[0]['TRPAT_KEY_CODE'], 'level' => $data_general[0]['TRPAT_KEY_TYPE']]) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="KEY_CODE" value="{{ $data_general[0]['TRPAT_KEY_CODE'] }}">
        <input type="hidden" name="KEY_TYPE" value="{{ $data_general[0]['TRPAT_KEY_TYPE'] }}">
        <div class="card-body">
            @foreach($data_attribute as $custom)
                <div class="form-group row">
                    <div class="col-lg-5">
                        <label>Masking Label:</label>
                        <input type="text" class="form-control" value="{{ $custom['TRPAT_MASKING'] }}" name="old_custom_masking[]" placeholder="Input masking">
                    </div>
                    <div class="col-lg-5">
                        <label>Value:</label>
                        <textarea type="text" class="form-control" name="old_custom_value[]" placeholder="Input value">{{ $custom['TRPAT_VALUE'] }}</textarea>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label><br>
                        <button data-code="{{ $custom['TRPAT_ID'] }}" data-action="{{ route('product_attribute_lab_delete') }}" class="btn btn-sm font-weight-bolder delete_btn btn-light-danger"><i class="la la-trash-o"></i>Delete</button>
                    </div>
                </div>
            @endforeach
            <div class="form-group row">
                <div id="repeater" class="col-md-12">
                    <div id="repeater">
                        <div data-repeater-list="CUSTOM">
                            <div data-repeater-item class="form-group row align-items-center">
                                <div class="col-lg-5">
                                    <label>Masking Label:</label>
                                    <input type="text" class="form-control" name="new_custom_masking" placeholder="Input masking">
                                </div>
                                <div class="col-lg-5">
                                    <label>Value:</label>
                                    <textarea type="text" class="form-control" name="new_custom_value" placeholder="Input value"></textarea>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label><br>
                                    <a href="javascript:;" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger">
                                        <i class="la la-trash-o"></i>Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row col-md-12">
                            <label>&nbsp;</label><br>
                            <a href="javascript:;" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-primary">
                                <i class="la la-plus"></i>Add
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="button" id="submit_custom_btn" class="btn btn-primary mr-2">Save Custom</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection