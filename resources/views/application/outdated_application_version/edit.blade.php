@extends('layouts.app')

@section('page_title', 'Create Brand')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/application/outdated_application_version/select2.js') }}"></script>
<script src="{{ asset('custom/js/application/outdated_application_version/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Form Add Outdated Version</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("outdated_application_version_update") }}" data-form-success-redirect="{{ route("outdated_application_version_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MAVER_ID" value="<?php echo $data["MAVER_ID"]; ?>">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Application Text:</label>
                            <input type="text" class="form-control" name="MAVER_TEXT" value="<?php echo $data["MAVER_TEXT"]; ?>" placeholder="Input application text">
                        </div>
                        <div class="col-lg-3">
                            <label>Application Version:</label>
                            <input type="text" class="form-control" name="MAVER_APP_VERSION" value="<?php echo $data["MAVER_APP_VERSION"]; ?>" placeholder="Input application version">
                        </div>
                        <div class="col-lg-3">
                            <label>Application Type:</label>
                            <select class="form-control select2" id="app_type" name="MAVER_APP_TYPE">
                                <option></option>
                                <option <?php if ($data["MAVER_APP_TYPE"] == 1) { echo("selected"); } ?> value="1">Customer</option>
                                <option <?php if ($data["MAVER_APP_TYPE"] == 2) { echo("selected"); } ?> value="2">Operation</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label>Operating System:</label>
                            <select class="form-control select2" id="operating_system" name="MAVER_OS_TYPE">
                                <option></option>
                                <option <?php if ($data["MAVER_OS_TYPE"] == 1) { echo("selected"); } ?> value="1">Android</option>
                                <option <?php if ($data["MAVER_OS_TYPE"] == 2) { echo("selected"); } ?> value="2">IOS</option>
                            </select>
                        </div>
                       
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-9">
                            <label>Operating System:</label>
                            <textarea class="form-control" name="MAVER_NOTES" rows="3"><?php echo($data["MAVER_NOTES"]); ?></textarea>
                        </div>
                        <div class="col-lg-3">
                            <label>Priority:</label>
                            <select class="form-control select2" id="priority" name="MAVER_IS_PRIORITY">
                                <option></option>
                                <option <?php if ($data["MAVER_IS_PRIORITY"] == 0) { echo("selected"); } ?> value="0">Low Priority</option>
                                <option <?php if ($data["MAVER_IS_PRIORITY"] == 1) { echo("selected"); } ?> value="1">High Priority</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("outdated_application_version_view") }}" class="btn btn-secondary">Back</a>
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
