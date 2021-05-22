<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Test Lab Hard Copy Result</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        @media print {
            @page {
                size: A4;
            }
        }

        ul {
            padding: 0;
            margin: 0 0 1rem 0;
            list-style: none;
        }

        body {
            font-family: "Inter", sans-serif;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table table tr {
            vertical-align: top;
        }

        .detail-table table th,
        .detail-table table td {
            text-align: justify;
            padding: 4px 0px;
        }

        .detail-table table th {
            width: 180px;
        }

        .result-table table,
        .result-table table th,
        .result-table table td {
            border: 1px solid silver;
        }

        .result-table table th,
        .result-table table td {
            padding: 4px 8px;
            font-size: 12px;
        }

        .result-table {
            margin-top: 30px;
        }

        h4,
        p {
            margin: 0;
        }

        .container {
            padding: 20px 0;
            width: 1000px;
            max-width: 90%;
            margin: 0 auto;
        }

        .inv-title {
            padding: 10px;
            border: 1px solid silver;
            text-align: center;
            margin-bottom: 30px;
        }

        .inv-logo-container {
            height: 60px;
            width: 60px;
            text-align: center;
            vertical-align: middle;
        }

        .inv-qr-container-zeta {
            height: 100px;
            margin-bottom: 50px;
        }

        .inv-qr-container-alpha {
            height: 100px;
        }

        .inv-logo {
            height: 60px;
            margin: auto;
        }

        /* header */
        .inv-header {
            margin-bottom: 20px;
        }

        .inv-header h2 {
            font-size: 20px;
            margin: 0 0 0.3rem 0;
        }

        .inv-header th,
        .inv-header td {
            font-size: 12px;
        }

        .inv-header ul li {
            font-size: 15px;
            padding: 3px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="inv-title">
            <table>
                <tr>
                    <td>
                        <div class="inv-logo-container">
                            <img src="{{ asset('logo-cek-ori.png') }}" alt="CekOri Logo" title="CekOri Logo" class="inv-logo">
                        </div>
                    </td>
                    <td style="width: 100%; text-align: center;">
                        <h4 style="margin: 0;">Test Lab Result {{ $data[0]["SCDET_MPRVE_TEXT"] }}</h4>
                        <h4 style="margin: 0;">{{ $data[0]["SCHED_MBRAN_NAME"] }} on {{ $data[0]["SCDET_SUBPA_SCAN_TIMESTAMP"] }}</h4>
                    </td>
                    <td>
                        <div class="inv-logo-container">
                            @if($brand_logo != null)
                                <img style="background-color:black;" src="{{ asset('storage/images/brand_logo') }}/{{ $brand_logo }}" alt="Brand Logo" title="Brand Logo" class="inv-logo">
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <!-- <p style="font-size: 10px;">Printed on {{ date('Y-m-d H:i:s') }}</p> -->
        <table>
            <tr>
                <td style="vertical-align: top;">
                    <h3>Hospital</h3>
                    <div class="inv-header">
                        <div class="detail-table">
                            <table>
                                <tr>
                                    <th>Hospital Company</th>
                                    <td>{{ $data[0]["SCHED_MCOMP_NAME"] }}</td>
                                </tr>
                                <tr>
                                    <th>Hospital Brand</th>
                                    <td>{{ $data[0]["SCHED_MBRAN_NAME"] }}</td>
                                </tr>
                                <tr>
                                    <th>Testing Center</th>
                                    <td>{{ $data[0]["SCDET_MABPR_MAPLA_TEXT"] }}</td>
                                </tr>
                                <tr>
                                    <th>Laboratorium Center</th>
                                    <td>{{ $data[0]["SCDET_SUBPA_MAPLA_TEXT"] }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <h3>Patient Data</h3>
                    <div class="inv-header">
                        <div class="detail-table">
                            <table>
                                <tr>
                                    <th>Patient</th>
                                    <td>{{ $data[0]["SCDET_MPRVE_TEXT"] }}</td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>{{ $data[0]["SCDET_MPRDT_TEXT"] }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth</th>
                                    <td>{{ $data[0]["SCDET_MPRMO_TEXT"] }}</td>
                                </tr>
                                <tr>
                                    <th>NIK</th>
                                    <td>{{ $data[0]["SCDET_MPRVE_SKU"] }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <h3>Test Lab Information</h3>
                    <div class="inv-header">
                        <div class="detail-table">
                            <table>
                                <tr>
                                    <th>Testing Doctor</th>
                                    <td>{{ $data[0]["SCDET_MABPR_ADMIN_TEXT"] }}</td>
                                </tr>
                                <tr>
                                    <th>Laboratorium Doctor</th>
                                    <td>{{ $data[0]["SCDET_SUBPA_ADMIN_TEXT"] }}</td>
                                </tr>
                                <tr>
                                    <th>Testing Date</th>
                                    <td>{{ $data[0]["SCDET_MABPR_SCAN_TIMESTAMP"] }}</td>
                                </tr>
                                <tr>
                                    <th>Testing Staff</th>
                                    <td>{{ $data[0]["SCDET_MABPR_STAFF_TEXT"] }}</td>
                                </tr>
                                <tr>
                                    <th>Laboratorium Date</th>
                                    <td>{{ $data[0]["SCDET_SUBPA_SCAN_TIMESTAMP"] }}</td>
                                </tr>
                                <tr>
                                    <th>Laboratorium Staff</th>
                                    <td>{{ $data[0]["SCDET_SUBPA_STAFF_TEXT"] }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <td style="width: 150px; padding-left: 20px;">
                    <table>
                        
                        <tr>
                            <td style="text-align: center;">
                                <span style="font-size: 12px;">ZETA</span>
                                <div class="inv-qr-container-zeta">
                                    <img src="data:image/png;base64, <?php echo $qr_image_zeta; ?>" class="center" style="width:100px; height: 100px;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <span style="font-size: 12px;">ALPHA</span>
                                <div class="inv-qr-container-alpha">
                                    <img src="data:image/png;base64, <?php echo $qr_image_alpha; ?>" class="center" style="width:100px; height: 100px;">
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <h3>Test Lab Result</h3>
        <div class="result-table detail-table">
            <table>
                <tr>
                    <th>Test Lab Type</th>
                    <td style="font-weight: bold;">Result</td>
                </tr>
                @foreach($data as $row)
                    <tr>
                        <th>{{ $row["SCDET_MPRCA_TEXT"] }}</th>
                        <td>{{ $row["SCDET_MPRVE_NOTES"] }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</body>

</html>