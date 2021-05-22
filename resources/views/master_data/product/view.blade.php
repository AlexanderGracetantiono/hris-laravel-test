@extends('layouts.app')

@section('page_title', 'View Product')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/product/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            
            <h3 class="card-label">Master Data Product</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route("master_data_product_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Product
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Product Code</th>
                    <th>Category</th>
                    <th>Product Name</th>
                    <th>Status</th>
                    <th width="10px" data-priority="1">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr <?php if ($row['MPRDT_STATUS'] != 1) { echo ("style='background-color:#eeeeee'");} ?>>
                    <td>{{ $row["MPRDT_CODE"] }}</td>
                    <td>{{ $row["MPRDT_MPRCA_TEXT"] }}</td>
                    <td>{{ $row["MPRDT_TEXT"] }}</td>
                    <td>@if ($row["MPRDT_STATUS"] == 1)
                        Active
                    @else
                        Inactive
                    @endif</td>
                    <td nowrap="nowrap">
                        <a href="{{ route("master_data_product_edit", ['code'=> $row["MPRDT_CODE"]]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Product"> <i
                            class="la la-edit"></i>
                        </a>
                        <button type="button" data-code="{{ $row['MPRDT_CODE'] }}" data-action="{{ route('master_data_product_delete') }}"
                            class="btn btn-sm btn-clean btn-icon delete_btn"   title="Delete Product"> <i class="la la-trash"></i>
                        </button>
                        @if($check_access == true)
                            <a href="{{ route('product_attribute_view', ['code' => $row['MPRDT_CODE'], 'level' => '2']) }}" class="btn btn-sm btn-clean btn-icon" title="Product Attribute"> <i
                                class="la la-list-alt"></i>
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
