@extends('layouts.app')

@section('page_title', 'View Testing Center / Laboratorium')

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

            <h3 class="card-label">Master Data Testing Center / Laboratorium</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route("master_data_lab_plant_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add Testing Center / Laboratorium
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Testing Center / Laboratorium Code</th>
                    <th>Testing Center / Laboratorium Name</th>
                    <th>Testing Center / Laboratorium Type</th>
                    <th data-priority="1" width="30px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr <?php if ($row['MAPLA_STATUS'] != 1) { echo ("style='background-color:#eeeeee'");} ?>>
                    <td>{{ $row["MAPLA_CODE"] }}</td>
                    <td>{{ $row["MAPLA_TEXT"] }}</td>
                    <td>
                        @if ($row["MAPLA_TYPE"] == 1)
                            Testing Center
                        @else
                            Laboratorium
                        @endif
                    </td>
                    <td nowrap="nowrap">
                        <a href="{{ route('master_data_lab_plant_edit', ['code'=> $row['MAPLA_CODE']]) }}" class="btn btn-sm btn-clean btn-icon" title="Edit Plant"> <i class="la la-edit"></i></a>
                        <button type="button" data-code="{{ $row['MAPLA_CODE'] }}" data-action="{{ route('master_data_lab_plant_delete') }}"
                            class="btn btn-sm btn-clean btn-icon delete_btn" title="Delete Plant"> <i class="la la-trash"></i>
                        </button>
                        <a target="_blank" href="http://maps.google.com/?q={{ $row['MAPLA_LAT'] }}" class="btn btn-sm btn-clean btn-icon" title="Location"> <i class="la la-map"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
