@extends('layouts.app')

@section('page_title', 'Edit Brand')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/vendor/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/vendor/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Form Edit Brand</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_vendor_update") }}" data-form-success-redirect="{{ route("master_data_vendor_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MVNDR_CODE" value="{{ $data['MVNDR_CODE'] }}">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-5">
                            <label>Company:</label>
                            <select class="form-control select2" id="MVNDR_MCOMP_CODE" name="MVNDR_MCOMP_CODE">
                                <option></option>
                                @foreach ($company as $row)
                                    <option value="{{ $row["MCOMP_CODE"] }}" @if ($row["MCOMP_CODE"] == $data["MVNDR_MCOMP_CODE"])
                                        selected
                                    @endif>{{ $row["MCOMP_NAME"] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-5">
                            <label>Brand Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="MVNDR_NAME" placeholder="Example: PT Sanggar Indah Indonesia" value="{{ $data["MVNDR_NAME"] }}">
                        </div>
                        <div class="col-lg-2">
                            <label class="mb-5">Status:</label>
                            <div class="radio-inline">
                                <label class="radio radio-md">
                                    <input type="radio" name="MVNDR_STATUS" value="1" @if ($data["MVNDR_STATUS"] == 1)
                                        checked
                                    @endif/>
                                    <span></span>
                                    Active
                                </label>
                                <label class="radio radio-md">
                                    <input type="radio" name="MVNDR_STATUS" value="0" @if ($data["MVNDR_STATUS"] == 0)
                                        checked
                                    @endif/>
                                    <span></span>
                                    Inactive
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Brand Address:</label>
                            <textarea class="form-control" maxlength="1000" placeholder="Example: Jl Bongo 2 Blok A" name="MVNDR_ADDRESS" rows="3">{{ $data["MVNDR_ADDRESS"] }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_vendor_view") }}" class="btn btn-secondary">Back</a>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button type="button" id="submit_btn" class="btn btn-primary mr-2">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
