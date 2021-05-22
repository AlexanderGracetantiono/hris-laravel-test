@extends('layouts.app')

@section('page_title', 'Edit Test Lab Type Form')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product_categories/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/product_categories/select2.js') }}"></script>

@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit Test Lab Type Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route('master_data_lab_category_update') }}" data-form-success-redirect="{{ route('master_data_lab_category_view') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="category_id" value="{{ $category_data["MPRCA_ID"] }}">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Brand:</label>
                            <input type="text" readonly class="form-control" value="<?php echo session('brand_name'); ?>">
                        </div>
                        <div class="col-lg-6">
                            <label>Test Lab Code:</label>
                            <input type="text" readonly class="form-control" name="category_code" placeholder="Input test lab type code" value="{{ $category_data["MPRCA_CODE"]}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Test Lab Type:</label>
                            <input type="text" class="form-control" name="category_name" placeholder="Input test lab type name" value="{{ $category_data["MPRCA_TEXT"]}}">
                        </div>
                        <div class="col-lg-6">
                            <label>Status:</label>
                            <div class="row">
                                <div class="col-9 col-form-label">
                                    <div class="radio-inline">
                                        <label class="radio radio-primary">
                                            <input type="radio" value="1" name="category_status_is_active" @if ($category_data["MPRCA_STATUS"]==1) checked="checked" @endif>
                                            <span></span>Active</label>
                                        <label class="radio radio-primary">
                                            <input type="radio" value="0" name="category_status_is_active" @if ($category_data["MPRCA_STATUS"]==0) checked="checked" @endif>
                                            <span></span>Non Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route('master_data_lab_category_view') }}" class="btn btn-secondary">Back</a>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button id="submit_btn" type="submit" class="btn btn-primary mr-2">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection