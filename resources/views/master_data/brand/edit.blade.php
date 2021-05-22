@extends('layouts.app')

@section('page_title', 'Edit Brand')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/vendor/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/vendor/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/vendor/image_upload.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit Brand Data</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_brand_update") }}" data-form-success-redirect="{{ route("master_data_brand_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MBRAN_CODE" value="<?php echo $data["MBRAN_CODE"]; ?>">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Company:</label>
                            <input type="text" maxlength="255" name="MBRAN_MCOMP_NAME" class="form-control" value="<?php echo $data['MBRAN_MCOMP_NAME']; ?>" readonly>
                        </div>
                        <div class="col-lg-4">
                            <label>Brand Name:</label>
                            <input type="text" maxlength="255" class="form-control" value="<?php echo $data['MBRAN_NAME']; ?>" name="MBRAN_NAME" placeholder="Example: Zara" required>
                            <input hidden type="text" maxlength="255" class="form-control" value="<?php echo $data['MBRAN_NAME']; ?>" name="MBRAN_NAME_ORIGINAL" placeholder="Example: Zara" required>
                        </div>
                        <div class="col-lg-4">
                            <label>Brand Type:</label>
                            <input type="hidden" value="<?php echo $data['MBRAN_TYPE']; ?>" name="MBRAN_TYPE">
                            @if ($data['MBRAN_TYPE'] == 1) 
                                <input type="text" value="Manufacture" class="form-control" disabled>
                            @elseif ($data['MBRAN_TYPE'] == 2) 
                                <input type="text" value="Lab Test" class="form-control" disabled>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-8">
                            <label>Brand Address:</label>
                            <textarea class="form-control" maxlength="1000" placeholder="Example: Jl Bongo 2 Blok A" name="MBRAN_ADDRESS" rows="5"><?php echo $data['MBRAN_ADDRESS']; ?></textarea>
                        </div>
                        <div class="col-lg-4">
                            <label class="mb-5">Status:</label>
                            <div class="radio-inline">
                                <label class="radio radio-md">
                                    <input type="radio" name="MBRAN_STATUS" value="1" @if ($data["MBRAN_STATUS"] == 1)
                                        checked
                                    @endif/>
                                    <span></span>
                                    Active
                                </label>
                                <label class="radio radio-md">
                                    <input type="radio" name="MBRAN_STATUS" value="0" @if ($data["MBRAN_STATUS"] == 0)
                                        checked
                                    @endif/>
                                    <span></span>
                                    Inactive
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_brand_view") }}" class="btn btn-secondary">Back</a>
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
