@extends('layouts.app')

@section('page_title', 'Detail Pengguna Backend')

@push('styles')

@endpush

@push('scripts')

@endpush

@section('content')
<div class="d-flex flex-row">
    <div class="flex-row-auto offcanvas-mobile w-250px w-xxl-350px" id="kt_profile_aside">
        <div class="card card-custom card-stretch">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-end">
                    <div class="dropdown dropdown-inline">
                    </div>
                </div>
                <div class="d-flex align-items-center" style="padding-top:20px;">
                    <div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
                        <div class="symbol-label" style="background-image:url({{ asset('storage/images/profile_pic')}}/{{$user_data["backend_user_profile_picture"]}})"></div>
                        <i class="symbol-badge bg-success"></i>
                    </div>
                    <div>
                    <a href="#" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">{{ $user_data["backend_user_name"] }}</a>
                        <div class="text-muted">
                            @if ($user_data["backend_user_role"] == 1)
                            Super Admin
                            @elseif ($user_data["backend_user_role"] == 2)
                                Admin
                            @endif
                        </div>
                        
                    </div>
                </div>
                <div class="py-9">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2">Email:</span>
                        <a href="#" class="text-muted text-hover-primary">{{ $user_data["backend_user_email"] }}</a>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold mr-2">Phone:</span>
                        <span class="text-muted">{{ $user_data["backend_user_phone_number"] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex-row-fluid ml-lg-8">
        <div class="card card-custom card-stretch">
            <div class="card-header py-3">
                <div class="card-title align-items-start flex-column">
                    <h3 class="card-label font-weight-bolder text-dark">Detail Akun</h3>
                    <span class="text-muted font-weight-bold font-size-sm mt-1">Detail informasi data pengguna</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <label class="col-xl-3"></label>
                    <div class="col-lg-9 col-xl-6">
                        <h5 class="font-weight-bold mb-6">Data Pribadi</h5>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Nama</label>
                    <div class="col-lg-9 col-xl-6">
                        <input class="form-control form-control-lg form-control-solid" readonly type="text" value="{{ $user_data["backend_user_name"] }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Email</label>
                    <div class="col-lg-9 col-xl-6">
                        <input class="form-control form-control-lg form-control-solid" type="text" readonly value="{{ $user_data["backend_user_email"] }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Nomor Telepon</label>
                    <div class="col-lg-9 col-xl-6">
                        <input class="form-control form-control-lg form-control-solid" type="text" readonly value="{{ $user_data["backend_user_phone_number"] }}">
                    </div>
                </div>
                <div class="row">
                    <label class="col-xl-3"></label>
                    <div class="col-lg-9 col-xl-6">
                        <h5 class="font-weight-bold mt-10 mb-6">Biografi</h5>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Biografi</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="input-group input-group-lg input-group-solid">
                            <textarea type="text" class="form-control form-control-lg form-control-solid">{{ $user_data["backend_user_biography"] }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-xl-3"></label>
                    <div class="col-lg-9 col-xl-6">
                        <h5 class="font-weight-bold mt-10 mb-6">Informasi Akun</h5>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Role</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="input-group input-group-lg input-group-solid">
                            <input type="text" class="form-control form-control-lg form-control-solid" readonly 
                            @if ($user_data["backend_user_role"] == 1)
                            value="Super Admin"
                            @elseif ($user_data["backend_user_role"] == 2)
                            value="Admin"
                            @endif>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Status</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="input-group input-group-lg input-group-solid">
                            <input type="text" class="form-control form-control-lg form-control-solid" readonly 
                            @if ($user_data["backend_user_is_active"] == 1)
                            value="Aktif"
                            @elseif ($user_data["backend_user_is_active"] == 0)
                            value="Non Aktif"
                            @endif>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Terakhir Login</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="input-group input-group-lg input-group-solid">
                            <input type="text" class="form-control form-control-lg form-control-solid" readonly value="{{ $user_data["backend_user_last_login"] }}">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Terakhir Login dengan IP</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="input-group input-group-lg input-group-solid">
                            <input type="text" class="form-control form-control-lg form-control-solid" readonly value="{{ $user_data["backend_user_last_login_ip_address"] }}">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Dibuat Oleh</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="input-group input-group-lg input-group-solid">
                            <input type="text" class="form-control form-control-lg form-control-solid" readonly value="{{ $user_data["backend_user_created_by_name"] }}">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Dibuat Pada</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="input-group input-group-lg input-group-solid">
                            <input type="text" class="form-control form-control-lg form-control-solid" readonly value="{{ $user_data["backend_user_created_time"] }}">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Terakhir Diubah Oleh</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="input-group input-group-lg input-group-solid">
                            <input type="text" class="form-control form-control-lg form-control-solid" readonly value="{{ $user_data["backend_user_changed_by_name"] }}">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Terakhir Diubah Oleh</label>
                    <div class="col-lg-9 col-xl-6">
                        <div class="input-group input-group-lg input-group-solid">
                            <input type="text" class="form-control form-control-lg form-control-solid" readonly value="{{ $user_data["backend_user_changed_time"] }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection