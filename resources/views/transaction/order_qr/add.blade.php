@extends('layouts.app')

@section('page_title', 'Create Brand')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/transaction/qr/select2.js') }}"></script>
<script src="{{ asset('custom/js/transaction/qr/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Form Order QR</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("order_qr_save") }}" data-form-success-redirect="{{ route('order_qr_view') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Quantity:</label>
                            <input type="text" class="form-control numeric_input" name="TRORD_QTY" placeholder="Enter Quantity">
                            <span <span style="color:red">* Only able to order 1080 QR</span>
                        </div>
                        {{-- <div class="col-md-4">
                            <label>Orientation:</label>
                            <select class="form-control select2" id="orientation_select2" name="TRORD_ORIENTATION">
                                <option></option>
                                <option value="portrait">Portrait</option>
                                <option value="landscape">Landscape</option>
                            </select>
                        </div> --}}
                        <div class="col-lg-6">
                            <label>QR Size<span <span style="color:red">* </span>:</label>
                            <input id="QR_SIZE" type="text" class="form-control numeric_input" name="TRORD_SIZE"
                                placeholder="Enter size (milimeter)">
                            <span style="color:red" id="minimum_size_text"></span><br>
                            <span style="color:red" id="maximum_size_text"></span>
                            {{-- <input id="QR_CODE" hidden type="text" class="form-control numeric_input"
                                value="{{ $code }}" name="code" placeholder="Enter Size"> --}}
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("order_qr_view") }}" class="btn btn-secondary">Back</a>
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
