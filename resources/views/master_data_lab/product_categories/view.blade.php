@extends('layouts.app')

@section('page_title', 'View Test Lab Type')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product_categories/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/product_categories/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            
            <h3 class="card-label">Master Data Test Lab Type</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('master_data_lab_category_add') }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Test Lab Type
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Test Lab Type Code</th>
                    <th>Test Lab Type Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($product_categories_data as $product_cat)
                <tr <?php if ($product_cat['MPRCA_STATUS'] != 1) { echo ("style='background-color:#eeeeee'");} ?> >
                    <td>
                        <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">
                            {{ $product_cat["MPRCA_CODE"] }}
                        </span>
                    </td>
                    <td>
                        <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">
                            {{ $product_cat["MPRCA_TEXT"] }}
                        </span>
                    </td>
                    <td>
                        <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">
                        @if ($product_cat["MPRCA_STATUS"] == 1)
                            Active
                        @else
                            Inactive
                        @endif
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('master_data_lab_category_edit', ['maprca_id' => $product_cat['MPRCA_ID']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Category"> <i
                            class="la la-edit"></i>
                        </a>
                        <button type="button" data-code="{{ $product_cat['MPRCA_ID'] }}" data-action="{{ route('master_data_lab_category_delete') }}"
                            class="btn btn-sm btn-clean btn-icon delete_btn"   title="Delete Category"> <i class="la la-trash"></i>
                        </button>
                        @if($check_access == true)
                            <a href="{{ route('product_attribute_view', ['code' => $product_cat['MPRCA_CODE'], 'level' => '1']) }}" class="btn btn-sm btn-clean btn-icon" title="Product Attribute"> <i
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
