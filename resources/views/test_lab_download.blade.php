<html>
<head>
    <style>
        .col {
            width: 68px;
            height: 48px;
            display:flex;
        }

        td {
            padding: 0; 
            margin: 0;
        }

        .qr {
            border: 0.5px solid black;
        }
    </style>
</head>
<body>
<table>
    <tr>
        <td class="qr">
            <div class="col">
                <div>
                    <img src="{{ asset('border_alpha.jpeg') }}" height="48" width="20">
                </div>
                <div style="text-align:center;padding-left:20px;padding-top:1px">
                    <figcaption style="font-size:2px;color:"><?php echo $brand; ?></figcaption>
                    <div style="margin-bottom: 1px;margin-top: 0.5px;">
                        <img src="data:image/png;base64, <?php echo $alpha; ?>">
                    </div>
                    <figcaption style="font-size:2px;color:">ALPHA</figcaption>
                </div>
            </div>
        </td>
        <td class="qr">
            <div class="col">
                <div>
                    <img src="{{ asset('border_zeta.jpeg') }}" height="48" width="20">
                </div>
                <div style="text-align:center;padding-left:20px;padding-top:1px">
                    <figcaption style="font-size:2px;color:"><?php echo $brand; ?></figcaption>
                    <div style="margin-bottom: 1px;margin-top: 0.5px;">
                        <img src="data:image/png;base64, <?php echo $zeta; ?>">
                    </div>
                    <figcaption style="font-size:2px;color:">Zeta</figcaption>
                </div>
            </div>
        </td>
    </tr>
</table>
</body>
</html>