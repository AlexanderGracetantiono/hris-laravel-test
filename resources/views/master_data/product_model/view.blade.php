@extends('layouts.app')

@section('page_title', 'View Product Model')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/product_model/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/product_model/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            
            <h3 class="card-label">Master Data Product Model</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route("master_data_product_model_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Product Model
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Model Code</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Model Name</th>
                    <th>Status</th>
                    <th width="10px" data-priority="1">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr <?php if ($row['MPRMO_STATUS'] != 1) { echo ("style='background-color:#eeeeee'");} ?>>
                        <td>{{ $row["MPRMO_CODE"] }}</td>
                        <td>{{ $row["MPRMO_MPRCA_TEXT"] }}</td>
                        <td>{{ $row["MPRMO_MPRDT_TEXT"] }}</td>
                        <td>{{ $row["MPRMO_TEXT"] }}</td>
                        <td>@if ($row["MPRMO_STATUS"] == 1)
                                Active
                            @else
                                Inactive
                            @endif
                        </td>
                        <td nowrap="nowrap">
                            <a href="{{ route('master_data_product_model_edit', ['code' => $row['MPRMO_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Product Model"> <i
                                class="la la-edit"></i>
                            </a>
                            <button type="button" data-code="{{ $row['MPRMO_CODE'] }}" data-action="{{ route('master_data_product_model_delete') }}"
                                class="btn btn-sm btn-clean btn-icon delete_btn"   title="Delete Product Model"> <i class="la la-trash"></i>
                            </button>
                            @if($check_access == true)
                                <a href="{{ route('product_attribute_view', ['code' => $row['MPRMO_CODE'], 'level' => '3']) }}" class="btn btn-sm btn-clean btn-icon" title="Product Attribute"> <i
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
