<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Certificate of Editor</title>
    <style>
        body {
            font-family: Verdana, Arial, sans-serif;
            /* line-height: 1.1; */
            padding: 30px;
        }

        h2 {
            text-align: center;
        }

        .signature {
            font-size: 14px;
        }
        /* .kotak {
           border: 2px solid #000;
        } */
    </style>
</head>

<body>
    <div style="position: absolute; top: -34; left: -34; width: 109%; height: 113%; z-index: -1;">
        <img src="{{ public_path('ext_images/bg_certificate_editor.png') }}"
            style="width: 100%; height: 100%; object-fit: cover;" alt="">
    </div>

    <p style="top: 198px; left: 258px; position: absolute; font-size: 14px;">
        No. B-{{ $number }}/Un.26.10/HM.001/{{ $month_roman }}/{{ $year }}
    </p>

    <div class="kotak" style="top: 230px; left: 68px; position: absolute;  width: 60%;  text-align: center; ">

        <p style="font-size: 18px; margin-bottom: 10px;">
            This is to certify that
        </p>
        <p style="font-size: 36px; color: #2B3092; font-weight: bold; margin: 0;">
            {{ $name }}
        </p>
        <p style="font-size: 18px; color: #F38120; margin: -2px 0 0 0; font-weight: bold;">
            <em>{{ $affiliation }}</em>
        </p>

        <p style="font-size: 17px; color: #000000; margin: 25px 0 0 0; font-weight: bold;">
            <em>has served as a dedicated member of the Editorial Board of the journal:</em>
        </p>
        <p style="font-size: 20px; color: #000000; margin: 3px 0 0 0; font-weight: bold;" >
            {{ $journal }}
        </p>
        <p style="font-size: 17px; color: #000000; margin: 3px 0 0 0;">
            and has significantly contributed to maintaining the scientific quality and integrity of the journal through
            editorial oversight and academic leadership.
        </p>
        <p style="font-size: 17px; color: #000000; margin: 10px 0 0 0;">
            Position: [Editorial Board Member] | Service Period : {{ $edition }}
        </p>
    </div>

</body>

</html>
