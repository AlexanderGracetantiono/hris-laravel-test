@extends('layouts.app')

@section('page_title', 'Lab Result ')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_store/activation_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_store/datatable.js?=1') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Detail Lab Result</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <h4>1.Test Lab Data</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-4">
                        <label>Testing Center:</label>
                        <input disabled type="text" value="{{ $data[0]['SCDET_MABPR_MAPLA_TEXT'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-lg-4">
                        <label>Testing Date:</label>
                        @if(isset($data[0]['SCDET_MABPR_SCAN_TIMESTAMP']))
                            <input disabled type="text" value="{{ substr($data[0]['SCDET_MABPR_SCAN_TIMESTAMP'],0,10) }}" class="form-control">
                        @else
                            <input disabled type="text" value="" class="form-control">
                        @endif
                    </div>
                    <div class="col-lg-4">
                        <label>Testing Time:</label>
                        @if(isset($data[0]['SCDET_MABPR_SCAN_TIMESTAMP']))
                            <input disabled type="text" value="{{ substr($data[0]['SCDET_MABPR_SCAN_TIMESTAMP'],11,5) }}" class="form-control">
                        @else
                            <input disabled type="text" value="" class="form-control">
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Responsible Testing Lab Doctor:</label>
                        <input disabled type="text" value="{{ $data[0]['SCDET_MABPR_ADMIN_TEXT'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label>Testing Center Staff:</label>
                        <input disabled type="text" value="{{ $data[0]['SCDET_MABPR_STAFF_TEXT'] ?? '' }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <br><h4>2.Laboratorium Data</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-4">
                        <label>Laboratorium Center:</label>
                        <input disabled type="text" value="{{ $data[0]['SCDET_SUBPA_MAPLA_TEXT'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-lg-4">
                        <label>Laboratorium Date:</label>
                        @if(isset($data[0]['SCDET_SUBPA_SCAN_TIMESTAMP']))
                            <input disabled type="text" value="{{ substr($data[0]['SCDET_SUBPA_SCAN_TIMESTAMP'],0,10) ?? '' }}" class="form-control">
                        @else
                            <input disabled type="text" value="" class="form-control">
                        @endif
                    </div>
                    <div class="col-lg-4">
                        <label>Laboratorium Time:</label>
                        @if(isset($data[0]['SCDET_SUBPA_SCAN_TIMESTAMP']))
                            <input disabled type="text" value="{{ substr($data[0]['SCDET_SUBPA_SCAN_TIMESTAMP'],11,5) ?? '' }}" class="form-control">
                        @else
                            <input disabled type="text" value="" class="form-control">
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Responsible Laboratorium Doctor:</label>
                        <input disabled type="text" value="{{ $data[0]['SCDET_SUBPA_ADMIN_TEXT'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label>Laboratorium Staff:</label>
                        <input disabled type="text" value="{{ $data[0]['SCDET_SUBPA_STAFF_TEXT'] ?? '' }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <br><h4>3. Patient Data</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Patient:</label>
                        <input disabled type="text" value="{{ $data[0]['SCDET_MPRVE_TEXT'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label>Gender:</label>
                        <input disabled type="text" value="{{ $data[0]['SCDET_MPRDT_TEXT'] ?? '' }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Date Of Birth:</label>
                        <input disabled type="text" value="{{ $data[0]['SCDET_MPRMO_TEXT'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label>NIK:</label>
                        <input disabled type="text" value="{{ $data[0]['SCDET_MPRVE_SKU'] ?? '' }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <br><h4>4.Test Lab</h4>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable1">
                            <thead>
                                <tr>
                                    <th>Test Lab Type</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $row)
                                    <tr>
                                        <td>{{ $row["SCDET_MPRCA_TEXT"] }}</td>
                                        <td>{{ $row["SCDET_MPRVE_NOTES"] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route("master_data_lab_batch_store_view") }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
