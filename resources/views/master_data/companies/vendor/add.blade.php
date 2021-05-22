@extends('layouts.app')

@section('page_title', 'Create Brand')

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
                <h3 class="card-title">Login Account Brand Data</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_vendor_save") }}" data-form-success-redirect="{{ route("master_data_vendor_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Company:</label>
                            <select class="form-control select2" id="MVNDR_MCOMP_CODE" name="MVNDR_MCOMP_CODE">
                                <option></option>
                                @foreach ($company as $row)
                                    <option value="{{ $row['MCOMP_CODE'] }}">{{ $row['MCOMP_NAME'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label>Brand Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="MVNDR_NAME" placeholder="Example: PT Dunlopillo Indonesia" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Brand Address:</label>
                            <textarea class="form-control" maxlength="1000" placeholder="Example: Jl Bongo 2 Blok A" name="MVNDR_ADDRESS" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_vendor_view") }}" class="btn btn-secondary">Back</a>
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
