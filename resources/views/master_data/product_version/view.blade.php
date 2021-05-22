@extends('layouts.app')

@section('page_title', 'View Product Version')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product_version/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/product_version/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            
            <h3 class="card-label">Master Data Product Version</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route("master_data_product_version_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Product Version
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Version Code</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Model</th>
                    <th>Version Name</th>
                    <th>SKU</th>
                    <th class="none">Description</th>
                    <th>Status</th>
                    <th width="10px" data-priority="1">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr <?php if ($row['MPRVE_STATUS'] != 1) { echo ("style='background-color:#eeeeee'");} ?>>
                        <td>{{ $row["MPRVE_CODE"] }}</td>
                        <td>{{ $row["MPRVE_MPRCA_TEXT"] }}</td>
                        <td>{{ $row["MPRVE_MPRDT_TEXT"] }}</td>
                        <td>{{ $row["MPRVE_MPRMO_TEXT"] }}</td>
                        <td>{{ $row["MPRVE_TEXT"] }}</td>
                        <td>{{ $row["MPRVE_SKU"] }}</td>
                        <td>{{ $row["MPRVE_NOTES"] }}</td>
                        <td>@if ($row["MPRVE_STATUS"] == 1)
                                Active
                            @else
                                Inactive
                            @endif
                        </td>
                        <td nowrap="nowrap">
                            <a href="{{ route('master_data_product_version_edit', ['code' => $row['MPRVE_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Product Version"> <i
                                class="la la-edit"></i>
                            </a>
                            <button type="button" data-code="{{ $row['MPRVE_CODE'] }}" data-action="{{ route('master_data_product_version_delete') }}"
                                class="btn btn-sm btn-clean btn-icon delete_btn"   title="Delete Product Version"> <i class="la la-trash"></i>
                            </button>
                            @if($check_access == true)
                                <a href="{{ route('product_attribute_view', ['code' => $row['MPRVE_CODE'], 'level' => '4']) }}" class="btn btn-sm btn-clean btn-icon" title="Product Attribute"> <i
                                    class="la la-list-alt"></i>
                                </a>
                                <a href="{{ route('product_custom_attribute_view', ['code' => $row['MPRVE_CODE'], 'level' => '4']) }}" class="btn btn-sm btn-clean btn-icon" title="Custom Product Attribute"> <i
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
