@extends('layouts.app')

@section('page_title', 'View Company')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/companies/datatable.js?=1') }}"></script>
<script src="{{ asset('custom/js/master_data/companies/delete_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">

            <h3 class="card-label">Master Data Company</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route("master_data_company_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Company
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Company Code</th>
                    <th>Company Name</th>
                    <th>Company Phone Number</th>
                    <th>PIC Name</th>
                    <th>PIC Email</th>
                    <th>PIC Mobile Phone Number</th>
                    <th>Status</th>
                    <th data-priority="1">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr <?php if ($row['MCOMP_STATUS'] != 1) { echo ("style='background-color:#eeeeee'");} ?>>
                    <td>{{ $row["MCOMP_CODE"] }}</td>
                    <td>{{ $row["MCOMP_NAME"] }}, {{ $row["MCOMP_TYPE"] }}</td>
                    <td>{{ $row["MCOMP_MACOP_CODE"].$row["MCOMP_AREA_NUMBER"].$row["MCOMP_OFFICE_PHONE_NUMBER"] }}</td>
                    <td>{{ $row["MCOMP_PIC_NAME"] }}</td>
                    <td>{{ $row["MCOMP_PIC_EMAIL"] }}</td>
                    <td>{{ $row["MCOMP_PIC_MACOP_CODE"].$row["MCOMP_PIC_PHONE_NUMBER"] }}</td>
                    <td>
                        @if ($row["MCOMP_STATUS"] == 1)
                            Active
                        @elseif ($row["MCOMP_STATUS"] == 2)
                            Waiting Approval
                        @else
                            Inactive
                        @endif
                    </td>
                    <td nowrap="nowrap">
                        <a href="{{ route('master_data_company_edit', ['code' => $row['MCOMP_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Company"> <i
                            class="la la-edit"></i>
                        </a>
                        <!-- <a href="{{ route('master_data_company_edit_pic', ['code' => $row['MCOMP_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Reset Login Password"> <i
                            class="la la-refresh"></i>
                        </a> -->
                        <!-- <button type="button" data-code="{{ $row['MCOMP_CODE'] }}" data-action="{{ route('master_data_company_delete') }}" class="btn reset_pic_button btn-sm btn-clean btn-icon"   title="Reset Login Password"> <i
                            class="la la-refresh"></i>
                        </button> -->
                        {{-- <button type="button" data-code="{{ $row['MCOMP_CODE'] }}" data-action="{{ route('master_data_company_delete') }}"
                            class="btn btn-sm btn-clean btn-icon delete_btn"   title="Delete Company"> <i class="la la-trash"></i>
                        </button> --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
