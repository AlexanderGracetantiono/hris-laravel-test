<html>

<head>
    <style>
        .col {
            width: 68px;
            height: 48px;
            display: flex;
        }

        td {
            padding: 0;
            margin: 0;
        }

        .qr {
            /* width: 46px;
            height: 64px; */
            overflow: hidden;
        }

        .qr_top {
            position: absolute;
            border: 0.5px solid black;
            border-radius: 5px;

        }
        .image_container {
            border-bottom-right-radius: 5px;
            border-bottom-left-radius: 5px;
            overflow: hidden;
        }
        .container {
        display: table;
        width: 100%;
        }

        .left-half {
        position: absolute;
        left: 0px;
        width: 50%;

        }

        .right-half {
        position: absolute;
        right: 0px;
        width: 50%;
        text-align: right;
        }
    </style>
</head>

<body>
    <?php
    // $paper_width = 297;
    // $paper_height =	420;
    $paper_width = 329;
    $paper_height =	483;
    $margin_left_right_default=12;
    $margin_top_bottom_default=12;
    $margin_left_right=6;
    $margin_top_bottom=2;
    $height_variable = 17/12;
    $qr_height = $border_size*$height_variable;
    

    $row_count = round(($paper_width - ((2*$margin_left_right)+(2*$margin_left_right_default)))/($border_size+5));
    $col_count = round(($paper_height - (+(2*$margin_top_bottom_default)))/($qr_height+5));

    $page_counter=1;
    $counter = 0;
    //old

    $border_width = 0.5;
    $padding_height = 1;
    $width_addition = $qr_size / 6.499999999999999;
    $width_addition_img = $qr_size / 5.714285714285714 - 0.2;
    $height_addition = $qr_size / 1.6666666666666666666667;
    $height_addition_border = $qr_size / 1.7777777777777777777778;
    $font_size = round($qr_size / 13.33333333333333) - 1;
    $ratio_border_width = $qr_size / 0.8695652173913043;
    $padding_top = $qr_size / 1.818181818181818;
    $font_container_height = $qr_size / 1.333333333333333;
    $padding_left_qr = $qr_size / 10 + 0.5;

    $padding_left_text_container = 1;
    $height_addition_img = 0.5;
    if ($qr_size > 60) {
    $border_width = $qr_size / 100;
    $padding_height = $qr_size / 45;
    };
    if ($qr_size > 50) {
    $height_addition_img = $qr_size / 85;
    $padding_left_text_container = $qr_size / 30;
    $padding_left_qr += $padding_left_text_container - 1;
    };
    if ($qr_size > 120) {
    $padding_height = $qr_size / 45 + $qr_size / 120;
    };
    $qr_vertical_margin = 3.3;
    $header_footer_height=4;
    switch ($border_size) {
        case 13:
        $qr_vertical_margin=3.8;
        $col_count-=1;
        $header_footer_height=6;
    break;
        case 14:
        $qr_vertical_margin=3.8;
        $header_footer_height=7;
        break;
        case 15:
        $row_count-=1;
        $qr_vertical_margin=3.8;
        $header_footer_height=8;
        break;
        case 16:
        $row_count-=1;
        $col_count-=1;
        $qr_vertical_margin=3.8;
        $header_footer_height=9;
        break;
        case 17:
        $col_count-=1;
        $qr_vertical_margin=3.8;
        $header_footer_height=10;
        break;
        case 18:
        $row_count-=1;
        break;
        case 19:
        $qr_vertical_margin=3.5;
        $header_footer_height=8;
        break;
        case 20:
        $row_count-=1;
        $col_count-=1;
        $qr_vertical_margin=3.9;
        $header_footer_height=11;
        break;
        case 21:
        $header_footer_height=6;
        break;
        case 22:
        $col_count-=1;
        $qr_vertical_margin=3.8;
        $header_footer_height=11;
        break;
        case 23:
        $header_footer_height=6;
        break;
        case 24:
        $col_count-=1;
        $qr_vertical_margin=3.9;
        $header_footer_height=13;
    break;
}
    ?>
    <?php for ($i = 0; $i < count($sticker_code); $i++) { ?> <?php if
        ($counter % ($row_count * $col_count)==0) { ?> 
        <div style="height: 4mm;">

        </div>
        <section class="container">
            <div class="left-half">
                {{-- <figcaption style="position: absolute; top: -{{$header_footer_height}}mm;font-size:12px;color:black">
                    <?php echo $file_name; ?>
                </figcaption> --}}
                <figcaption style="position: absolute; bottom: 0px; font-size:12px;color:black">
                    <?php echo $file_name; ?>
                </figcaption>
            </div>
            <div class="right-half">
                {{-- <figcaption style="position: absolute; top: -{{$header_footer_height}}mm;right:0px;font-size:12px;color:black;font-weight: bold">
                    <?php echo $page_counter; ?>
                </figcaption> --}}

                <figcaption style="position: absolute; bottom: 0px;right: 0px;font-size:12px;color:black;font-weight: bold">
                    <?php echo $page_counter; ?>
                </figcaption>
            </div>
        </section>
        <table style="padding-left: {{$margin_left_right}}mm;padding-right: {{$margin_left_right}}mm;">
        <?php } ?>
        <?php if ($counter % $row_count == 0) { ?>
        <tr>
            <?php } ?>
            <td class="qr"
            style="width: {{$border_size}}mm;height:{{$qr_height}}mm">
                <div class="row">
                    <div style="padding-bottom: {{ $padding_height + 1 }}px">
                        <div
                            style="position: absolute;width: 10px;height: 10px;padding-top: {{ $padding_top }}px;padding-left: {{ $padding_left_text_container }}px;">
                            <div
                                style="transform: translateX(-45%) translateY(-90%) rotate(-90deg);width: {{ $font_container_height }}px;">
                                <figcaption
                                    style="font-size:{{ $font_size }}px;color:<?php echo $color; ?>">
                                    <?php echo $company_name; ?>
                                </figcaption>
                            </div>
                        </div>
                        <div style="padding-top:{{ $padding_height }}px;padding-left: {{ $padding_left_qr }}px">
                            <img
                                src="data:image/png;base64,<?php echo $sticker_code[$i]['image']; ?>">
                        </div>
                    </div>
                    <div class="image_container">
                        <img src="{{ $border }}"
                            height="{{ $qr_size / 2.105263157894737 + $height_addition_img }}px"
                            width="{{ $qr_size + $width_addition_img }}px">
                    </div>
                </div>
                <div class="qr_top"
                style="width: {{ $border_size+0.3 }}mm;height:{{ $qr_height-0.3 }}mm; border-width: {{ $border_width }}px">
                </div>
            </td>
            <?php if (($counter + 1) % $row_count != 0) { ?>
            <td style="width:4mm;">
            </td>
            <?php } ?>

            <?php $counter++; ?>
            <?php if ($counter % $row_count == 0 || $counter == count($sticker_code)) { ?>
        </tr>
        <?php if ($counter % ($row_count * $col_count) != 0) { ?>
            <tr>
                <td style="height: {{$qr_vertical_margin}}mm;">
                </td>
            </tr>
            
        <?php } ?>
        <?php } ?>
        <?php if ($counter % ($row_count * $col_count) == 0) { ?>
        </table>
        <div style="height: {{$header_footer_height}}mm;">
        </div>
        <?php $page_counter+=1; }  ?>
        <?php } ?>
        
</body>

</html>