<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06F;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            /* border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15); */
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: center;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }


        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

    </style>
</head>

<body>
    <div class="invoice-box">
        <h3>Hospital Data</h3>
        <table style="padding-bottom:10px;text-allign:center">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Brand</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $data["TRQRA_MCOMP_TEXT"] }}</td>
                    <td>{{ $data["TRQRA_MBRAN_TEXT"] }}</td>
                </tr>
            </tbody>
        </table>
        <h3>Patient Data</h3>
        <table style="padding-bottom:10px;text-allign:center">
            <thead>
                <tr>
                    <th>Test Type</th>
                    <th>Patient</th>
                    <th>Gender</th>
                    <th>Date Of Birth</th>
                    <th>NIK</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $data["TRQRA_MPRCA_TEXT"] }}</td>
                    <td>{{ $data["TRQRA_MPRVE_TEXT"] }}</td>
                    <td>{{ $data["TRQRA_MPRDT_TEXT"] }}</td>
                    <td>{{ $data["TRQRA_MPRMO_TEXT"] }}</td>
                    <td>{{ $data["TRQRA_MPRVE_SKU"] }}</td>
                </tr>
            </tbody>
        </table>
        <h3 style="padding-bottom:10px">Result : {{ $data["TRQRA_MPRVE_NOTES"] }}</h3>
        <h3>Testing Center Data</h3>
        <table style="padding-bottom:10px;text-allign:center">
            <thead>
                <tr>
                    <th>Testing Center</th>
                    <th>Testing Date</th>
                    <th>Testing Doctor</th>
                    <th>Testing Staff</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $data["TRQRA_MAPLA_TEXT"] }}</td>
                    <td>{{ $data["TRQRA_EMP_SCAN_TIMESTAMP"] }}</td>
                    <td>{{ $data["TRQRA_NOTES"] }}</td>
                    <td>{{ $data["TRQRA_EMP_SCAN_TEXT"] }}</td>
                </tr>
            </tbody>
        </table>
        <h3>Laboratorium Data</h3>
        <table style="padding-bottom:30px;text-allign:center">
            <thead>
                <tr>
                    <th>Laboratorium Center</th>
                    <th>Laboratorium Date</th>
                    <th>Laboratorium Doctor</th>
                    <th>Laboratorium Staff</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $data["TRQRZ_MAPLA_TEXT"] }}</td>
                    <td>{{ $data["TRQRZ_EMP_SCAN_TIMESTAMP"] }}</td>
                    <td>{{ $data["TRQRZ_NOTES"] }}</td>
                    <td>{{ $data["TRQRZ_EMP_SCAN_TEXT"] }}</td>
                </tr>
            </tbody>
        </table>
        <table style="padding-bottom:30px;text-allign:center">
            <thead>
                <tr>
                    <th>Alpha</th>
                    <th>Zeta</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <center>
                            <img src="data:image/png;base64, <?php echo $qr_image_alpha; ?>" class="center" style="width:25%; max-width:300px;">
                        </center>
                    </td>
                    <td>
                        <center>
                            <img src="data:image/png;base64, <?php echo $qr_image_zeta; ?>" class="center" style="width:25%; max-width:300px;">
                        </center>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
</body>

</html>
