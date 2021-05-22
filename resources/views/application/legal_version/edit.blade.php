@extends('layouts.app')

@section('page_title', 'Edit Privacy Policy & Term Sercives Version')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/application/legal_version/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Form Edit Privacy Policy & Term Sercives Version</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("legal_version_update") }}" data-form-success-redirect="{{ route("legal_version_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MALVR_ID" value="<?php echo $data["MALVR_ID"]; ?>">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Privacy Policy Version:</label>
                            <input type="text" class="form-control" name="MALVR_PRIVACY_POLICY_VERSION" value="<?php echo $data["MALVR_PRIVACY_POLICY_VERSION"]; ?>" placeholder="Input privacy policy version">
                        </div>
                        <div class="col-lg-6">
                            <label>Term Sercives Version:</label>
                            <input type="text" class="form-control" name="MALVR_TERM_SERVICE_VERSION" value="<?php echo $data["MALVR_TERM_SERVICE_VERSION"]; ?>" placeholder="Input term services version">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("legal_version_view") }}" class="btn btn-secondary">Back</a>
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
