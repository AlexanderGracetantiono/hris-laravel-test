@extends('layouts.app')

@section('page_title', 'View Application Version')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/application/outdated_application_version/datatable.js?=1') }}"></script>
<!-- <script src="{{ asset('custom/js/master_data/brand/view/delete_ajax.js') }}"></script> -->
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">

            <h3 class="card-label">Outdated Application Version</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('outdated_application_version_add') }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Outdated Version
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Application Text</th>
                    <th>Application Version</th>
                    <th>Application Type</th>
                    <th>Operating System</th>
                    <th>Priority</th>
                    <th width="10px" data-priority="1">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr>
                    <td>{{ $row["MAVER_TEXT"] }}</td>
                    <td>{{ $row["MAVER_APP_VERSION"] }}</td>
                    <td>
                        @if ($row["MAVER_APP_TYPE"] == 1)
                            Customer
                        @else
                            Operasional
                        @endif
                    </td>
                    <td>
                        @if ($row["MAVER_OS_TYPE"] == 1)
                            Android
                        @else
                            IOS
                        @endif
                    </td>
                    <td>
                        @if ($row["MAVER_IS_PRIORITY"] == 1)
                            High Priority
                        @else
                            Low Priority
                        @endif
                    </td>
                    <td nowrap="nowrap">
                        <a href="{{ route('outdated_application_version_edit', ['code'=> $row['MAVER_ID']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Version"> <i
                            class="la la-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
