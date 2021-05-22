@extends('layouts.app')

@section('page_title', 'View Batch Store')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/batch_store/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/batch_store/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            
            <h3 class="card-label">Master Data Chat API</h3>
        </div>
        <div class="card-toolbar">
            {{-- <a href="{{ route("master_data_batch_store_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Batch Store
            </a> --}}
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>id</th>
                    <th>Text</th>
                    <th>Date</th>
                    <th data-priority="1" width="10px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row["id"] }}</td>
                        <td>{{ $row["text"] }}</td>
                        <td>{{ $row["send_date"] }}</td>
                        <td nowrap="nowrap">
                            <a href="{{ route('master_data_batch_store_detail',['code'=> $row['id']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Detail Chat"><i class="la la-eye"></i></a>
                            {{-- @if($row["MBSTR_ACTIVATION_STATUS"] == 1 && $row["MBSTR_STORE_ADMIN_CODE"] == session("user_code"))
                                <a href="{{ route('master_data_batch_store_edit', ['code'=> $row['id']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Chat"><i class="la la-edit"></i></a>
                            @else
                            @endif --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
