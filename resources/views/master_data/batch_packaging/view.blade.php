@extends('layouts.app')

@section('page_title', 'View Batch Acceptance')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/plant/view/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/plant/view/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            
            <h3 class="card-label">Master Data Batch Acceptance</h3>
        </div>
        <?php if (session('user_role') != "8") { ?>
            <!-- <div class="card-toolbar">
                <a href="{{ route("master_data_batch_packaging_add") }}" class="btn btn-primary font-weight-bolder">
                    <i class="fas fa-plus-circle"></i>Add Batch Acceptance
                </a>
            </div> -->
        <?php } ?>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Batch Code</th>
                    <th>Batch Name</th>
                    <th>Packaging Center</th>
                    <th>Status</th>
                    <th data-priority="1" width="10px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row["MABPA_CODE"] }}</td>
                        <td>{{ $row["MABPA_MABPR_TEXT"] }}</td>
                        <td>{{ $row["MABPA_MAPLA_TEXT"] }}</td>
                        <td>
                            @if ($row["MABPA_ACTIVATION_STATUS"] == 0)
                                Waiting To Be Accepted
                            @elseif ($row["MABPA_ACTIVATION_STATUS"] == 1)
                                Accepted
                            @else
                                Closed
                            @endif
                        </td>
                        <td nowrap="nowrap">
                            @if($row["MABPA_ACTIVATION_STATUS"] == 0)
                                <a href="{{ route('master_data_batch_packaging_edit', ['code'=> $row['MABPA_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Batch Packaging"><i class="la la-edit"></i></a>
                            @elseif($row["MABPA_ACTIVATION_STATUS"] == 1 || $row["MABPA_ACTIVATION_STATUS"] == 2)
                                <a href="{{ route('master_data_batch_packaging_detail',['code'=> $row['MABPA_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Detail Batch Packaging"><i class="la la-eye"></i></a>
                            @endif
                                <!-- <a href="{{ route('master_data_batch_packaging_scanned_qr',['code'=> $row['MABPA_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Scanned QR Batch Packaging"><i class="la la-qrcode"></i></a> -->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection