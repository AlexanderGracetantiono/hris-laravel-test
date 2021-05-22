@extends('layouts.app')

@section('page_title', 'Create Company')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/companies/image_upload.js') }}"></script>
<script src="{{ asset('custom/js/master_data/companies/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/companies/select2.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Form Create Company</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_company_save") }}" data-form-success-redirect="{{ route("master_data_company_view") }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <h4>1. Company Data</h4><br>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Company Type:</label>
                            <input type="text" class="form-control" name="MCOMP_TYPE" placeholder="Example: PT">
                        </div>
                        <div class="col-lg-6">
                            <label>Company Name:</label>
                            <input type="text" class="form-control" name="MCOMP_NAME" placeholder="Example: Sanggar Indah Indonesia">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Company NPWP:</label>
                            <input type="text" class="form-control numeric_input" name="MCOMP_NPWP_NUMBER" placeholder="Example: 123456789876543">
                        </div>
                        <div class="col-lg-6">
                            <label>Company Phone Number:</label>
                            <div class="input-group">
                                <div class="input-group-prepend mr-1">
                                    <select class="form-control select2 country_code" name="MCOMP_MACOP_CODE">
                                        <?php for ($i = 0; $i < count($code); $i++) { ?>
                                            <option value="<?php echo $code[$i]["MACOP_CODE"] ?>">
                                                <?php echo $code[$i]["MACOP_CODE"] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    &nbsp;&nbsp;
                                    <input size="2" type="text" class="form-control numeric_input area_number" name="MCOMP_AREA_NUMBER" placeholder="21">
                                </div>
                                <input type="text" class="form-control numeric_input phone_number" name="MCOMP_OFFICE_PHONE_NUMBER" placeholder="3500300">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Company Address:</label>
                            <textarea class="form-control" placeholder="Example: Jl Bongo 2 Blok A" name="MCOMP_OFFICE_ADDRESS" rows="11"></textarea>
                        </div>
                        <div class="col-lg-6">
                            <label>Upload Company NPWP:</label><br>
                            <div class="image-input image-input-outline" id="MCOMP_NPWP">
                                <div class="image-input-wrapper" style="background-image: url({{asset('image-placeholder.png')}})"></div>
                                <label class="btn btn-md btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change"   title="" data-original-title="Ubah">
                                    <i class="fa fa-pen icon-lg text-muted"></i>
                                    <input type="file" name="MCOMP_NPWP" accept=".png, .jpg, .jpeg">
                                    <input type="hidden" name="profile_avatar_remove">
                                </label>
                                <span class="btn btn-md btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel"   title="" data-original-title="Batal">
                                    <i class="ki ki-bold-close icon-lg text-muted"></i>
                                </span>
                                <span class="btn btn-md btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove"   title="" data-original-title="Hapus">
                                    <i class="ki ki-bold-close icon-lg text-muted"></i>
                                </span>
                            </div>
                            <br>
                            <label class="text-danger">*Maximal File Size 2MB</label>
                        </div>
                    </div>
                    <br><h4>2. PIC Data</h4><br>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>PIC Name:</label>
                            <input type="text" class="form-control" name="MCOMP_PIC_NAME" placeholder="Example: Michael">
                        </div>
                        <div class="col-lg-4">
                            <label>PIC Email:</label>
                            <input type="text" class="form-control" name="MCOMP_PIC_EMAIL" placeholder="Example: michael@gmail.com">
                        </div>
                        <div class="col-lg-4">
                            <label>PIC Mobile Phone Number:</label>
                            <div class="input-group">
                                <div class="input-group-prepend mr-3">
                                    <select class="form-control select2 country_code" name="MCOMP_PIC_MACOP_CODE">
                                        <?php for ($i = 0; $i < count($code); $i++) { ?>
                                            <option value="<?php echo $code[$i]["MACOP_CODE"] ?>">
                                                <?php echo $code[$i]["MACOP_CODE"] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <input type="text" class="form-control numeric_input phone_number" name="MCOMP_PIC_PHONE_NUMBER" placeholder="Example: 0812477219">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_company_view") }}" class="btn btn-secondary">Back</a>
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
