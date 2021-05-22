@extends('layouts.app')

@section('page_title', 'View Pool Product')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/pool_product/datatable.js?=1') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <h3 class="card-label">Pool Product</h3>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product Description</th>
                    <th class="none">Category</th>
                    <th class="none">Product</th>
                    <th class="none">Model</th>
                    <th class="none">Version</th>
                    <th>Quantity</th>
                    <th>Quantity Left</th>
                    <th data-priority="1" width="10px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $data)
                    <tr>
                        <td>{{ $data["POPRD_MPRVE_SKU"] }}</td>
                        <td>{{ $data["POPRD_MPRVE_NOTES"] }}</td>
                        <td>{{ $data["POPRD_MPRCA_TEXT"] }}</td>
                        <td>{{ $data["POPRD_MPRDT_TEXT"] }}</td>
                        <td>{{ $data["POPRD_MPRMO_TEXT"] }}</td>
                        <td>{{ $data["POPRD_MPRVE_TEXT"] }}</td>
                        <td>{{ $data["POPRD_QTY"] }}</td>
                        <td>{{ $data["POPRD_QTY_LEFT"] }}</td>
                        <td nowrap="nowrap">
                            <a href="{{ route('master_data_pool_product_detail',['code' => $data['POPRD_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Detail Pool Product"><i class="la la-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection