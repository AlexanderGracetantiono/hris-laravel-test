@extends('layouts.app')

@section('page_title', 'View Privacy Policy & Term Sercives Version')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/application/legal_version/datatable.js?=1') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">

            <h3 class="card-label">Privacy Policy & Term Sercives Version</h3>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Privacy Policy Version</th>
                    <th>term Service Version</th>
                    <th width="10px" data-priority="1">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr>
                    <td>{{ $row["MALVR_PRIVACY_POLICY_VERSION"] }}</td>
                    <td>{{ $row["MALVR_TERM_SERVICE_VERSION"] }}</td>
                    <td nowrap="nowrap">
                        <a href="{{ route('legal_version_edit', ['code'=> $row['MALVR_ID']]) }}" class="btn btn-sm btn-clean btn-icon"   title="Edit Version"> <i
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
