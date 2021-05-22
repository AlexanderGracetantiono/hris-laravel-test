@extends('layouts.app')

@section('page_title', 'Approval QR')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/transaction/qr/notes.js') }}"></script>
<script src="{{ asset('custom/js/transaction/qr/select2.js') }}"></script>
<script src="{{ asset('custom/js/transaction/qr/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Form Approval QR</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("order_qr_approval_process") }}" data-form-success-redirect="{{ route('order_qr_view') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="TRORD_CODE" value="{{ $data['TRORD_CODE'] }}">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Company:</label>
                            <input type="text" disabled class="form-control" value="{{ $data['TRORD_MCOMP_TEXT'] }}">
                        </div>
                        <div class="col-lg-3">
                            <label>Brand:</label>
                            <input type="text" disabled class="form-control" value="{{ $data['TRORD_MBRAN_TEXT'] }}">
                        </div>
                        <div class="col-lg-3">
                            <label>Quantity:</label>
                            <input type="text" disabled class="form-control" value="{{ $data['TRORD_QTY'] }}">
                        </div>
                        <div class="col-md-3">
                            <label>Approval Status:</label>
                            <select class="form-control select2" id="approval_status" name="TRORD_STATUS" >
                                <option></option>
                                <option value="2" <?php if ('title = "Approve"') ?> >Approve</option>
                                <option value="3">Reject</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Notes:</label>
                            <textarea class="form-control" name="TRORD_NOTES" rows="3" placeholder="Insert Notes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="{{ route("order_qr_view") }}" class="btn btn-secondary">Back</a>
                            <button type="button" id="submit_btn" class="btn float-right btn-primary mr-2">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
