<html>

<head>
    <style>
        .qr {
            overflow: hidden;
            /* border: 0.5px solid black;
            border-radius: 5px;
            padding: 0px; */
        }

        .col {
            display: flex;
        }

        td {
            padding: 0;
            margin: 0;
        }

        .qr_top {
            position: absolute;
            border: 0.5px solid black;
            border-radius: 5px;

        }

        .image_container {
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
            overflow: hidden;
        }

    </style>
</head>

<body>
    <?php
    $total_width_a3_body_only = 680; //px
    $total_height_a3_body_only = 1488; //px
    $counter = 0;

    $border_width = 0.5;
    $padding_height = $qr_size / 13.33333333333333;

    $height_addition = $qr_size / 5.714285714285714;
    $width_addition = $qr_size / 1.6666666666666666666667;

    $height_image = $qr_size + $qr_size / 5.714285714285714;
    $width_image = $qr_size / 2.6;

    $width_addition_border = round($qr_size / 1.851851851851852);
    $height_addition_border = $qr_size / 6.499999999999999;

    $font_size = $qr_size / 20;
    $ratio_border_width = $qr_size / 0.7851851851851852; //64px//17mm
    $ratio_border_height = $qr_size/0.6720647773279352; //45px//12mm

    $padding_left = $qr_size / 2.666666666666667;

    $row_count = round(round($total_width_a3_body_only / $ratio_border_width, 1));
    $col_count = round(round($total_height_a3_body_only / $ratio_border_height, 1));

    switch ($qr_size) {
    case 12:
        $row_count -= 1;
    break;
    case 50:
        $row_count -= 1;
    break;
    case 63:
        $row_count -= 1;
    break;
    case 70:
    $row_count -= 1;
    break;

    default:
    break;
    }
    // $col_count = 18;

    if ($qr_size > 60) {
    $border_width = $qr_size / 100;
    $padding_height = $qr_size / 45;
    }
    ?>
    <?php for ($i = 0; $i < count($qrcode); $i++) { ?> <?php if ($counter
        % $row_count==0) { ?> 
        <table style="padding-left: 22px;padding-right: 22px;">
        <?php } ?>
        <?php if ($counter % $row_count == 0) { ?>
        <tr>
            <?php } ?>
            <td>
                <div class="qr"
                    style=";width: {{ $qr_size + $width_addition }}px;height:{{ $qr_size + $height_addition }}px; border-width: {{ $border_width }}px">
                    <div class="col">
                        <div class="image_container">
                            <img src="{{ $border }}" height="{{ $height_image }}px"
                                width="{{ $width_image }}px">
                        </div>
                        <div style="transform: rotate(90deg) translateY(-15%);">
                            <div style="text-align:center;">
                                <figcaption
                                    style="font-size:{{ $font_size }}px;color:<?php echo $color; ?>;">
                                    <?php echo $company_name; ?>
                                </figcaption>
                                <div style="margin-bottom: 0.5px;margin-top: 0.5px;">
                                    <img
                                        src="data:image/png;base64, <?php echo $qrcode[$i]['image']; ?>">
                                </div>
                                <figcaption
                                    style="font-size:{{ $font_size }}px;color:<?php echo $color; ?>;">
                                    ALPHA{{$qr_size}}
                                </figcaption>
                            </div>
                        </div>
                        <div class="qr_top"
                            style="width: {{ $qr_size + $width_addition_border }}px;height:{{ $qr_size + $height_addition_border }}px; border-width: {{ $border_width }}px">
                        </div>
                    </div>
                </div>
            </td>
            <?php if (($counter + 1) % $row_count != 0) { ?>
            <td style="width:3mm;">
            </td>
            <?php } ?>

            <?php $counter++; ?>
            <?php if ($counter % $row_count == 0 || $counter == count($qrcode)) { ?>
        </tr>
        <tr>
            <?php for ($iii = 0; $iii < $row_count * 2 - 1; $iii++) { ?> <td
                style="height: 3mm;">
                </td>
                <?php } ?>
        </tr>
        <?php } ?>
        <?php if ($counter % $row_count == 0) { ?>
        </table>
        <?php } ?>

        <?php } ?>
</body>

</html>
