<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Certificate of Reviewer</title>
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
        <img src="{{ public_path('ext_images/bg_certificate_reviewer.png') }}"
            style="width: 100%; height: 100%; object-fit: cover;" alt="">
    </div>

    <p style="top: 198px; left: 380px; position: absolute; font-size: 14px;">
        No. B-{{ $number }}/Un.26.10/HM.001/{{ $month_roman }}/{{ $year }}
    </p>

    <div class="kotak" style="top: 230px; left: 192px; position: absolute;  width: 60%;  text-align: center; ">

        <p style="font-size: 18px; margin-bottom: 10px;">
            This is to certify that
        </p>
        <p style="font-size: 36px; color: #2B3092; font-weight: bold; margin: 0;">
            {{ $name }}
        </p>
        <p style="font-size: 18px; color: #00A551; margin: -2px 0 0 0; font-weight: bold;">
            <em>{{ $affiliation }}</em>
        </p>

        <p style="font-size: 17px; color: #000000; margin: 25px 0 0 0; font-weight: bold;">
            <em>has made a valuable contribution as a Peer Reviewer for the journal:</em>
        </p>
        <p style="font-size: 20px; color: #F38120; margin: 3px 0 0 0; font-weight: bold;">
            {{ $journal }}
        </p>
        <p style="font-size: 17px; color: #000000; margin: 3px 0 0 0;">
            by providing insightful and constructive evaluations of scientific manuscripts during the editorial review
            process.
        </p>
        <p style="font-size: 17px; color: #000000; margin: 10px 0 0 0;">
            <b>Service Period</b> : {{ $edition }}
        </p>
        <p style="font-size: 17px; color: #000000; margin:0;">
            <b>Articles Reviewed</b> : {{ $manuscript_count }} Manuscripts
        </p>
    </div>

    <div class="signature" style="position: absolute; bottom: 30px; left: 192px; width: 60%; text-align: center;">
        <img style="height: 90px;  margin:  10px 0 0 0;"src="{{ $chief_editor_signature }}" alt="">
        <p style="font-size: 17px; color: #000000; margin: 10px 0 0 0;"><strong>{{ $chief_editor }}</strong></p>
        <p style="font-size: 17px; color: #000000; margin: 2px 0 0 0;">Editor in Chief</p>
    </div>

</body>

</html>
