<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.5;
            padding: 30px;
        }

        h2 {
            text-align: center;
        }

        .signature {
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div style="position: absolute; top: -33; left: -34; width: 113%; height: 109%; z-index: -1;">
        <img src="{{ public_path('ext_images/bg_confirm.png') }}" style="width: 100%; height: 100%; object-fit: cover;"
            alt="">
    </div>
    <p style="top: 200px; left: 260px; position: absolute; font-size: 12px;">
        Number: {{ $id }}/LoA/JRNL/RJ/2024
    </p>

    <div style="top: 250px;  position: absolute; font-size: 14px;">
        <p>Dear Author,<br>
            <strong>{{ $name }}</strong><br>
            <em>{{ $affiliation }}</em>
        </p>

        <p style="margin-top: 40px;">
            Thank you for your cooperation in performing all the changes requested by the reviewers. At the same time,
            we gladly inform you that your paper entitled “ <strong>{{ $title }}</strong>” for which you are the
            correspondence author, was accepted for publication in {{ $journal }}.
        </p>

        <p style="margin-top: 30px;">Your article will be published in Edition {{ $editon }}.</p>

        <p style="margin-top: 20px;">Thank you for making Journal {{ $journal }} a vehicle for your
            research interests.</p>
    </div>

    <img style="position: absolute; bottom: 210; left: 40;  width: 120px;" src="{{ $journal_thumbnail }}" alt="">

    <div class="signature" style="position: absolute; bottom: 210; right: 85; ">
        <p>Bukittinggi, {{ $date }}</p>
        <p>Editor in Chief,</p>
        <p style="margin-top: 70px;"><strong>Dr. Nofiardi, M.Ag</strong></p>
    </div>
</body>

</html>
