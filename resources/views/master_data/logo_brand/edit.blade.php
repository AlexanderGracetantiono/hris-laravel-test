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
                <h3 class="card-title">Upload Brand Logo</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route('logo_brand_update') }}" data-form-success-redirect="{{ route("logo_brand_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MBRAN_CODE" value="<?php echo $data["MBRAN_CODE"]; ?>">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Company:</label>
                            <input type="text" maxlength="255" class="form-control" value="<?php echo $data['MBRAN_MCOMP_NAME']; ?>" readonly>
                        </div>
                        <div class="col-lg-4">
                            <label>Brand Name:</label>
                            <input type="text" maxlength="255" class="form-control" value="<?php echo $data['MBRAN_NAME']; ?>" readonly placeholder="Example: Zara" required>
                        </div>
                        <div class="col-lg-4">
                            <label>Brand Type:</label>
                            <input type="hidden" value="<?php echo $data['MBRAN_TYPE']; ?>">
                            @if ($data['MBRAN_TYPE'] == 1) 
                                <input type="text" value="Manufacture" class="form-control" disabled>
                            @elseif ($data['MBRAN_TYPE'] == 2) 
                                <input type="text" value="Lab Test" class="form-control" disabled>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Brand Address:</label>
                            <textarea class="form-control" maxlength="1000" readonly placeholder="Example: Jl Bongo 2 Blok A" rows="11"><?php echo $data['MBRAN_ADDRESS']; ?></textarea>
                        </div>
                        <div class="col-lg-6">
                            <label>Upload Brand Logo:</label><br>
                            <div class="image-input image-input-outline" id="MBRAN_IMAGE" >
                                @if($data["MBRAN_IMAGE"] == null)
                                    <div class="image-input-wrapper" style="width: 230px;height: 230px;background-image: url({{asset('image-placeholder.png')}})"></div>
                                @else
                                    <div class="image-input-wrapper" style="width: 230px;height: 230px;background-image: url({{asset('storage/images/brand_logo')}}/{{$data['MBRAN_IMAGE']}})"></div>
                                @endif
                                    <label class="btn btn-md btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Ubah">
                                        <i class="fa fa-pen icon-lg text-muted"></i>
                                        <input type="file" name="MBRAN_IMAGE" accept=".png, .jpg, .jpeg">
                                        <input type="hidden" name="profile_avatar_remove">
                                    </label>
                                    <span class="btn btn-md btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Batal">
                                        <i class="ki ki-bold-close icon-lg text-muted"></i>
                                    </span>
                                    <span class="btn btn-md btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="" data-original-title="Hapus">
                                        <i class="ki ki-bold-close icon-lg text-muted"></i>
                                    </span>
                                </div>
                            <br>
                            <label class="text-danger">*Maximal File Size 2MB</label><br>
                            <label class="text-danger">*Maximal Ukuran 300px x 300px</label>
                        </div>

                        
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button type="button" id="submit_btn" class="btn btn-primary mr-2">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
