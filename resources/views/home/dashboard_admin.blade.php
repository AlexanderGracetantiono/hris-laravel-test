@extends('layouts.app')

@section('page_title', 'Home Page')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/home_admin/datatable.js?=3') }}"></script>
<script src="{{ asset('custom/js/home_admin/select2.js') }}"></script>
<script src="{{ asset('custom/js/home_admin/location.js?=2') }}"></script>
<script src="https://maps.google.com/maps/api/js?key=AIzaSyAaeNBbhL14EMqp60bDRxu0ZF4X6c9EiPM"></script>
@endpush

@section('content')

<!-- Carding company & brand -->
<div class="row">
    <div class="col-xl-12">
        <div class="card card-custom card-stretch gutter-b">
            <div class="card-header">
                <h3 class="card-title">Total Company & Brand</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-9 bg-light-warning rounded p-5">
                            <i class="icon-xl fas fa-building text-warning mr-5"></i>
                            <div class="d-flex flex-column flex-grow-1 mr-2">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Total Company</span>
                                <span class="text-muted font-weight-bold">Total Company</span>
                            </div>
                            <span class="font-weight-bolder text-warning py-1 font-size-lg">{{ count($company) }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center bg-light-success rounded p-5 mb-9">
                            <i class="icon-xl fas fa-users text-success mr-5"></i>
                            <div class="d-flex flex-column flex-grow-1 mr-2">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Total Brand</span>
                                <span class="text-muted font-weight-bold">Total Brand</span>
                            </div>
                            <span class="font-weight-bolder text-success py-1 font-size-lg">{{ $count_brand }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Product PIC Brand -->
<div class="row">
    <div class="col-xl-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Filter Company</h3>
            </div>
            <form class="form" id="form" method="GET" action="{{ route('dashboard_admin') }}" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Company:</label>
                            <select class="form-control select2" id="company_select2" name="company">
                                <option></option>
                                @foreach($company as $company)
                                    <option @if($filter['company'] == $company['MCOMP_CODE']) selected @endif value="{{ $company['MCOMP_CODE'] }}">{{ $company['MCOMP_NAME'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label>Brand:</label>
                            <select class="form-control select2" id="brand_select2" name="brand">
                                <option></option>
                                @foreach($brand as $brand)
                                    <option @if($filter['brand'] == $brand['MBRAN_CODE']) selected @endif value="{{ $brand['MBRAN_CODE'] }}">{{ $brand['MBRAN_NAME'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button type="submit" class="btn btn-primary mr-2">Filter</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if($allow_product_filter == 1)
    <!-- product -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <h3 class="card-title">Filter Product</h3>
                </div>
                <form class="form" id="form" method="GET" action="{{ route('dashboard_admin') }}" enctype="multipart/form-data">
                    <input type="hidden" id="company" value="{{ $filter['company'] }}" name="company">
                    <input type="hidden" id="brand" value="{{ $filter['brand'] }}" name="brand">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-3">
                                @if($selected_brand_type == 1 || $selected_brand_type == 0)
                                    <label>Product Category:</label>
                                @elseif($selected_brand_type == 2)
                                    <label>Test Lab Type:</label>
                                @endif
                                <select class="form-control select2" id="product_category_select2" name="category">
                                    <option></option>
                                    @foreach($category as $category)
                                        <option @if($filter['category'] == $category['MPRCA_CODE']) selected @endif value="{{ $category['MPRCA_CODE'] }}">{{ $category['MPRCA_TEXT'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                @if($selected_brand_type == 1 || $selected_brand_type == 0)
                                    <label>Product:</label>
                                @elseif($selected_brand_type == 2)
                                    <label>Gender:</label>
                                @endif
                                <select class="form-control select2" id="product_select2" name="product">
                                    <option></option>
                                    @foreach($product as $product)
                                        <option @if($filter['product'] == $product['MPRDT_CODE']) selected @endif value="{{ $product['MPRDT_CODE'] }}">{{ $product['MPRDT_TEXT'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                @if($selected_brand_type == 1 || $selected_brand_type == 0)
                                    <label>Product Model:</label>
                                @elseif($selected_brand_type == 2)
                                    <label>Date Of Birth:</label>
                                @endif
                                <select class="form-control select2" id="product_model_select2" name="model">
                                    <option></option>
                                    @foreach($model as $model)
                                        <option @if($filter['model'] == $model['MPRMO_CODE']) selected @endif value="{{ $model['MPRMO_CODE'] }}">{{ $model['MPRMO_TEXT'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                @if($selected_brand_type == 1 || $selected_brand_type == 0)
                                    <label>Product Version:</label>
                                @elseif($selected_brand_type == 2)
                                    <label>Patient:</label>
                                @endif
                                <select class="form-control select2" id="product_version_select2" name="version">
                                    <option></option>
                                    @foreach($version as $version)
                                        <option @if($filter['version'] == $version['MPRVE_CODE']) selected @endif value="{{ $version['MPRVE_CODE'] }}">{{ $version['MPRVE_TEXT'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@if($data != null)
    <!-- Carding -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card card-custom card-stretch gutter-b">
                <div class="card-header">
                    <h3 class="card-title">Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-9 bg-light-warning rounded p-5">
                                <i class="icon-xl fas fa-list text-warning mr-5"></i>
                                <div class="d-flex flex-column flex-grow-1 mr-2">
                                    <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Total Counted Scan</span>
                                    <span class="text-muted font-weight-bold">Total Counted Scan</span>
                                </div>
                                <span class="font-weight-bolder text-warning py-1 font-size-lg">@if($data != null) {{ count($data) }} @else 0 @endif</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center bg-light-success rounded p-5 mb-9">
                                <i class="icon-xl fas fa-users text-success mr-5"></i>
                                <div class="d-flex flex-column flex-grow-1 mr-2">
                                    <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Unique User Scan</span>
                                    <span class="text-muted font-weight-bold">Unique User Scan</span>
                                </div>
                                <span class="font-weight-bolder text-success py-1 font-size-lg">{{ $total_user_scan }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center bg-light-danger rounded p-5 mb-9">
                                <i class="icon-xl fas fa-qrcode text-danger mr-5"></i>
                                <div class="d-flex flex-column flex-grow-1 mr-2">
                                    <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Unique QR Scan</span>
                                    <span class="text-muted font-weight-bold">Unique QR Scan</span>
                                </div>
                                <span class="font-weight-bolder text-danger py-1 font-size-lg">{{ $total_qr_scan }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center bg-light-info rounded p-5">
                                <i class="icon-xl fas fa-cubes text-info mr-5"></i>
                                <div class="d-flex flex-column flex-grow-1 mr-2">
                                    <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Unique Product Scan </span>
                                    <span class="text-muted font-weight-bold">Unique product scan </span>
                                </div>
                                <span class="font-weight-bolder text-info py-1 font-size-lg">{{ $total_variant_scan }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <h3 class="card-title">Map</h3>
                </div>
                <div class="card-body">
                    <div id="kt_gmap_1" style="height:500px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <h3 class="card-title">Detailed Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-checkable" id="kt_datatable1">
                        <thead>
                            <tr>
                                <th>Bridge Code</th>
                                <th>User Name</th>
                                <th>Scan Timestamp</th>
                                <th>Location</th>
                                <th>Brand</th>
                                <?php if ($selected_brand_type == 1) { ?>
                                    <th>Category</th>
                                    <th>Product</th>
                                    <th>Model</th>
                                    <th>Version</th>
                                    <th>SKU</th>
                                <?php } ?>
                                <?php if ($selected_brand_type == 2) { ?>
                                    <th>Test Lab Type</th>
                                    <th>Gender</th>
                                    <th>Date Of Birth</th>
                                    <th>Patient</th>
                                    <th>NIK</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $data)
                                <tr>
                                    <td>{{ $data["SCHED_MASCO_CODE"] }}</td>
                                    <td>{{ $data["SCLOG_CST_SCAN_TEXT"] }}</td>
                                    <td>{{ $data["SCLOG_CST_SCAN_TIMESTAMP"] }}</td>
                                    <td><a target="_blank" href="http://maps.google.com/?q={{ $data['SCLOG_CST_SCAN_LAT'] }},{{ $data['SCLOG_CST_SCAN_LNG'] }}">{{ $data["SCLOG_CST_SCAN_LAT"] }} , {{ $data["SCLOG_CST_SCAN_LNG"] }}</a></td>
                                    <td>{{ $data["SCHED_MBRAN_NAME"] }}</td>
                                    <td>{{ $data["SCDET_MPRCA_TEXT"] }}</td>
                                    <td>{{ $data["SCDET_MPRDT_TEXT"] }}</td>
                                    <td>{{ $data["SCDET_MPRMO_TEXT"] }}</td>
                                    <td>{{ $data["SCDET_MPRVE_TEXT"] }}</td>
                                    <td>{{ $data["SCDET_MPRVE_SKU"] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection