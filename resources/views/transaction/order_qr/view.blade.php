@extends('layouts.app')

@section('page_title', 'View Order QR')

    @push('styles')

    @endpush

    @push('scripts')
        <script src="{{ asset('custom/js/transaction/qr/datatable.js?=1') }}"></script>
        <script src="{{ asset('custom/js/transaction/qr/download_qr_ajax.js') }}"></script>
    @endpush

@section('content')
    <div class="card card-custom">
        <div class="card-header py-3">
            <div class="card-title">
                <h3 class="card-label">Order QR</h3>
            </div>
            <div class="card-toolbar">
                <?php if (session('user_role') == 3) { ?>
                <a href="{{ route('order_qr_add') }}" class="btn btn-primary font-weight-bolder">
                    <i class="fas fa-plus-circle"></i>Order QR
                </a>
                <?php } ?>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-checkable" id="kt_datatable1">
                <thead>
                    <tr>
                        <th>Order QR Code</th>
                        <th>Company Name</th>
                        <th>Brand Name</th>
                        <th>Quantity</th>
                        <th>File Ready</th>
                        @if (session('user_role') == 3)
                            <th width="10px" data-priority="1">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            <td>{{ $row['TRORD_CODE'] }}</td>
                            <td>{{ $row['TRORD_MCOMP_TEXT'] }}</td>
                            <td>{{ $row['TRORD_MBRAN_TEXT'] }}</td>
                            <td>{{ $row['TRORD_QTY'] }}</td>
                            <td>{{ $row['TRORD_ESTIMATED_TIME'] }}</td>
                            @if (session('user_role') == 3)
                                @if ($row['TRORD_STATUS'] == 1)
                                    <td nowrap="nowrap">
                                        {{-- <a href="{{ route('download_qr_alpha', ['code' => $row['TRORD_CODE'], 'qr_route' => 'download_qr_alpha']) }}"
                                            class="btn btn-sm btn-clean btn-icon" title="Download QR Alpha"> <i
                                                class="la la-qrcode"></i>
                                        </a>
                                        <a href="{{ route('download_qr_zeta', ['code' => $row['TRORD_CODE'], 'qr_route' => 'download_qr_zeta']) }}"
                                            class="btn btn-sm btn-clean btn-icon" title="Download QR Zeta"> <i
                                                class="la la-qrcode"></i>
                                        </a>
                                        <a href="{{ route('download_sticker_code', ['code' => $row['TRORD_CODE'], 'qr_route' => 'download_sticker_code']) }}"
                                            class="btn btn-sm btn-clean btn-icon" title="Download QR Bridge"> <i
                                                class="la la-qrcode"></i>
                                        </a> --}}
                                        <button type="button" data-code="{{ $row['TRORD_CODE'] }}" data-qr-type="alpha" data-action="{{ route('download_qr') }}"
                                            class="btn btn-sm btn-clean btn-icon download_qr_btn" title="Download QR Alpha"> <i class="la la-qrcode"></i>
                                        </button>
                                        <button type="button" data-code="{{ $row['TRORD_CODE'] }}" data-qr-type="zeta" data-action="{{ route('download_qr') }}"
                                            class="btn btn-sm btn-clean btn-icon download_qr_btn" title="Download QR Zeta"> <i class="la la-qrcode"></i>
                                        </button>
                                        <button type="button" data-code="{{ $row['TRORD_CODE'] }}" data-qr-type="bridge" data-action="{{ route('download_qr') }}"
                                            class="btn btn-sm btn-clean btn-icon download_qr_btn" title="Download QR Bridge"> <i class="la la-qrcode"></i>
                                        </button>
                                    </td>
                                @endif
                                @if ($row['TRORD_STATUS'] == 2)
                                    <td>Unavailable</td>
                                @endif
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
