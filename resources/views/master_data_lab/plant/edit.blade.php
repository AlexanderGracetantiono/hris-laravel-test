@extends('layouts.app')

@section('page_title', 'Edit Testing Center / Laboratorium Form')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data_lab/plant/add/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/brand/edit/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit Testing Center / Laboratorium Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route('master_data_lab_plant_update') }}" data-form-success-redirect="{{ route('master_data_lab_plant_view') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="<?php echo $data["MAPLA_ID"]; ?>" name="MAPLA_ID">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Testing Center / Laboratorium Code:</label>
                            <input type="text" maxlength="50" class="form-control remove_space" name="MAPLA_CODE" value="<?php echo $data["MAPLA_CODE"]; ?>"  readonly>
                        </div>
                        <div class="col-lg-4">
                            <label>Testing Center / Laboratorium Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="MAPLA_TEXT" value="<?php echo $data["MAPLA_TEXT"]; ?>" placeholder="Input Testing Center / Laboratorium name">
                        </div>
                        <div class="col-lg-4">
                            <label>Testing Center / Laboratorium Type:</label>
                            <select class="form-control select2" id="plant" name="MAPLA_TYPE">
                                <option></option>
                                    <option <?php if ($data["MAPLA_TYPE"] == 1) { echo("selected"); } ?> value="1">Testing Center</option>
                                    <option <?php if ($data["MAPLA_TYPE"] == 2) { echo("selected"); } ?> value="2">Laboratorium Center</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Testing Center / Laboratorium Phone Number:</label>
                            <div class="input-group">
                                <div class="input-group-prepend mr-3">
                                    <select class="form-control select2 country_code" name="MAPLA_MACOP_CODE">
                                        <?php for ($i = 0; $i < count($code); $i++) { ?>
                                            <option <?php if ($data["MAPLA_MACOP_CODE"] == $code[$i]["MACOP_CODE"]) { echo("selected"); } ?> value="<?php echo $code[$i]["MACOP_CODE"] ?>">
                                                <?php echo $code[$i]["MACOP_CODE"] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    &nbsp;
                                    <input size="2" type="text" class="form-control input-xs numeric_input" value="<?php echo $data["MAPLA_AREA_NUMBER"]; ?>" name="MAPLA_AREA_NUMBER" placeholder="21">
                                </div>
                                <input type="text" maxlength="255" class="form-control numeric_input" value="<?php echo $data["MAPLA_PHONE_NUMBER"]; ?>" name="MAPLA_PHONE_NUMBER" placeholder="Input Testing Center / Laboratorium phone number">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label>Location:</label>
                            <input type="text" maxlength="255" class="form-control" value="<?php echo $data["MAPLA_LAT"]; ?>" name="MAPLA_LAT" placeholder="Input latitude & longitude">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-9">
                            <label>Testing Center / Laboratorium Address:</label>
                            <textarea class="form-control" maxlength="1000" placeholder="Example: Jl Bongo 2 Blok A" name="MAPLA_ADDRESS" rows="3"><?php echo $data["MAPLA_ADDRESS"] ?></textarea>
                        </div>
                        <div class="col-lg-3">
                            <label class="mb-5">Status:</label>
                            <div class="radio-inline">
                                <label class="radio radio-md">
                                    <input type="radio" name="MAPLA_STATUS" value="1" @if ($data["MAPLA_STATUS"] == 1)
                                        checked
                                    @endif/>
                                    <span></span>
                                    Active
                                </label>
                                <label class="radio radio-md">
                                    <input type="radio" name="MAPLA_STATUS" value="0" @if ($data["MAPLA_STATUS"] == 0)
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
                            <a href="{{ route('master_data_lab_plant_view') }}" class="btn btn-secondary">Back</a>
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
