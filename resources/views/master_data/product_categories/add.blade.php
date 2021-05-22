@extends('layouts.app')

@section('page_title', 'Add Product Category Form')

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
                <h3 class="card-title">Add Product Category Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_category_save") }}" data-form-success-redirect="{{ route("master_data_category_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>Brand:</label>
                            <input disabled type="text" class="form-control" value="<?php echo session("brand_name"); ?>">
                        </div>
                        <div class="col-md-6">
                            <label>Category Name:</label>
                            <input type="text" class="form-control" name="category_name" placeholder="Input category name">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_category_view") }}" class="btn btn-secondary">Back</a>
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