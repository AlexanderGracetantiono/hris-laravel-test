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
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
/*         
        .invoice-box table tr.custom td:nth-child(3) {
            text-align: right;
        }
        .invoice-box table tr.custom td:nth-child(4) {
            text-align: right;
        } */

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

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
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
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="{{ asset('logo-cek-ori.png') }}" style="width:100px; max-width:300px;">
                            </td>
                            <td style="text-align:center;">
                                Print Timestamp : <?php echo date("Y-m-d H:i:s"); ?><br>
                                Batch Name: <?php echo $data["SUBPA_TEXT"]; ?><br>
                                Brand Name: <?php echo $data["SUBPA_MBRAN_TEXT"]; ?><br>
                                From : <?php echo $from_employee["MAEMP_TEXT"]; ?>, <?php echo $from_employee["MAEMP_USER_NAME"]; ?><br>
                                To : <?php echo $data["SUBPA_STORE_ADMIN_TEXT"]; ?><br>
                            </td>
                            <td class="title">
                                <img src="{{ asset('storage/images/brand_logo') }}/{{ $brand_logo }}" style="background-color:black;float:right;width:80px; max-width:300px;">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr class="heading">
                            <td><center>
                                Category
                            </center>
                            </td>
                            <td><center>
                                Product
                            </center>
                            </td>
                            <td><center>
                                Model
                            </center>
                            </td>
                            <td><center>
                                Version
                            </center>
                            </td>
                            <td><center>
                                SKU
                            </center>
                            </td>
                            <td><center>
                                Quantity
                            </center>
                            </td>
                        </tr>
                        <tr class="custom">
                            <td><center>
                                <?php echo $data["POPRD_MPRCA_TEXT"]; ?>
                                </center>
                            </td>
                            <td><center>
                                <?php echo $data["POPRD_MPRDT_TEXT"]; ?>
                                </center>
                            </td>
                            <td><center>
                                <?php echo $data["POPRD_MPRMO_TEXT"]; ?>
                            </center>
                            </td>
                            <td><center>
                                <?php echo $data["POPRD_MPRVE_TEXT"]; ?>
                            </center>
                            </td>
                            <td><center>
                                <?php echo $data["POPRD_MPRVE_SKU"]; ?>
                            </center>
                            </td>
                            <td><center>
                                <?php echo $count_paired_qr; ?>
                            </center>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
            <p class="center">Plant Packaging Address :<br>
            <?php echo($data["MAPLA_ADDRESS"]); ?></p>
        <br>
        <table cellpadding="0" cellspacing="0">
            <tr class="heading">
                <td><span style="opacity:0;color:#eeeeee">pad</span>Packaging Staff</td>
                <td><center>Driver</center><span style="opacity:0;color:#eeeeee">padng driver</span></td>
                <td><span style="opacity:0;color:#eeeeee">pading staff</span>Receiver</td>
            </tr>
        </table>
        <br><br><br><br><br><br>
        <table>
            <tr class="heading">
                <td><center>( <span style="opacity:0;color:#eeeeee">Prd. Warehouse Staff</span> )</center></td>
                <td><center>( <span style="opacity:0;color:#eeeeee">Prd. Warehouse Staff</span> )</center></td>
                <td><center>( <span style="opacity:0;color:#eeeeee">Prd. Warehouse Staff</span> )</center></td>
            </tr>
        </table>
        <p class="center">Scan For Detail Information</p>
        <img src="data:image/png;base64, <?php echo $qr_image; ?>" class="center" style="width:25%; max-width:300px;">
    </div>
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
</body>

</html>
