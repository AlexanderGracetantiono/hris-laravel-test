@extends('layouts.app')

@section('page_title', 'Add Production / Packaging Center Form')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/plant/add/map_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_packaging/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/plant/add/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Add Production / Packaging Center Form</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_plant_save") }}" data-form-success-redirect="{{ route("master_data_plant_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Production / Packaging Center Name:</label>
                            <input type="text" maxlength="255" class="form-control" name="MAPLA_TEXT" placeholder="Input production / packaging center name">
                        </div>
                        <div class="col-lg-6">
                            <label>Production / Packaging Center Type:</label>
                            <select class="form-control select2" id="plant" name="MAPLA_TYPE">
                                <option></option>
                                    <option value="1">Production Center</option>
                                    <option value="2">Packaging Center</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Production / Packaging Center Phone Number:</label>
                            <div class="input-group">
                                <div class="input-group-prepend mr-3">
                                    <select class="form-control select2 country_code" name="MAPLA_MACOP_CODE">
                                        <?php for ($i = 0; $i < count($code); $i++) { ?>
                                            <option value="<?php echo $code[$i]["MACOP_CODE"] ?>">
                                                <?php echo $code[$i]["MACOP_CODE"] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    &nbsp;&nbsp;
                                    <input size="2" type="text" class="form-control area_number input-xs numeric_input" name="MAPLA_AREA_NUMBER" placeholder="21">
                                </div>
                                <input type="text" maxlength="15" class="form-control numeric_input phone_number" name="MAPLA_PHONE_NUMBER" placeholder="Input phone number">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label>Location:</label>
                            <input type="text" maxlength="255" class="form-control" name="MAPLA_LAT" placeholder="Input latitude & Longitude">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Production / Packaging Center Address:</label>
                            <textarea class="form-control" maxlength="1000" placeholder="Example: Jl Bongo 2 Blok A" name="MAPLA_ADDRESS" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_plant_view") }}" class="btn btn-secondary">Back</a>
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
