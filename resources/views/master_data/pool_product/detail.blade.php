@extends('layouts.app')

@section('page_title', 'Detail Pool Product ')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/pool_product/activation_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/pool_product/datatable.js?=1') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Detail Pool Product</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <h4>1. Pool Product Detail</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Quantity:</label>
                        <input disabled type="text" class="form-control" value="{{ $data['POPRD_QTY'] }}">
                    </div>
                    <div class="col-lg-6">
                        <label>Quantity Left:</label>
                        <input disabled type="text" class="form-control" value="{{ $data['POPRD_QTY_LEFT'] }}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <h4>2. Product Data</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Category:</label>
                        <input disabled type="text" class="form-control" value="{{ $data['POPRD_MPRCA_TEXT'] }}">
                    </div>
                    <div class="col-lg-6">
                        <label>Product:</label>
                        <input disabled type="text" class="form-control" value="{{ $data['POPRD_MPRDT_TEXT'] }}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Model:</label>
                        <input disabled type="text" class="form-control" value="{{ $data['POPRD_MPRMO_TEXT'] }}">
                    </div>
                    <div class="col-lg-6">
                        <label>Version:</label>
                        <input disabled type="text" class="form-control" value="{{ $data['POPRD_MPRVE_TEXT'] }}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>SKU:</label>
                        <input disabled type="text" class="form-control" value="{{ $data['POPRD_MPRVE_SKU'] }}">
                    </div>
                    <div class="col-lg-6">
                        <label>Product Description:</label>
                        <textarea disabled type="text" rows="5" class="form-control">{{ $data['POPRD_MPRVE_NOTES'] }}</textarea>
                    </div>
                </div>
               
                <div class="form-group row">
                    <div class="col-md-12">
                        <br><h4>2. Batch Acceptance</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable2">
                            <thead>
                                <tr>
                                    <th>Batch Code</th>
                                    <th>Batch Quantity</th>
                                    <th>Accepted Timestamp</th>
                                    <th>Batch Production Notes</th>
                                    <th>Batch Production Discrepancy Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($batch_acceptance as $batch_acceptance)
                                    <tr>
                                        <td>{{ $batch_acceptance["MABPA_CODE"] }}</td>
                                        <td>{{ $batch_acceptance["MABPA_QTY"] }}</td>
                                        <td>{{ $batch_acceptance["MABPA_ACTIVATION_TIMESTAMP"] }}</td>
                                        <td>{{ $batch_acceptance["MABPR_NOTES"] }}</td>
                                        <td>{{ $batch_acceptance["MABPR_DISCREPANCY_NOTES"] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route("master_data_pool_product_view") }}" class="btn btn-secondary">Back</a>
                
                <br>
            </div>
        </div>
    </div>
</div>
@endsection
