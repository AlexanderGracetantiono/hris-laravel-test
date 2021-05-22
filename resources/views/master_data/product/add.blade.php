@extends('layouts.app')

@section('page_title', 'Add Product Form')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/product/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Add Product Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_product_save") }}" data-form-success-redirect="{{ route("master_data_product_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Brand Name:</label>
                            <input type="text" disabled class="form-control" value="<?php echo session("brand_name") ?>">
                        </div>
                        <div class="col-lg-4">
                            <label>Category:</label>
                            <select class="form-control select2" id="category" name="MPRDT_MPRCA_CODE" readonly>
                                <option></option>
                                <?php for ($j=0 ; $j < count($categories); $j++) {?>
                                    <option value="<?php echo $categories[$j]["MPRCA_CODE"] ?>"><?php echo $categories[$j]["MPRCA_TEXT"] ?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label>Product Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="MPRDT_TEXT" placeholder="Input product name">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_product_view") }}" class="btn btn-secondary">Back</a>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button type="button" id="submit_btn" class="btn btn-primary mr-2">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
