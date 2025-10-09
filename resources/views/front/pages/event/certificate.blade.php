<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        /* Mengatur font dari Google Fonts untuk tampilan yang lebih modern */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap');

        /* Page setup for A4 Landscape */
        @page {
            size: A4 landscape;
            margin: 0;
        }

        /* Menggunakan font dasar yang didukung PDF */
        body {
            font-family: DejaVu Sans, sans-serif;
            /* DejaVu Sans adalah font default di Dompdf yang mendukung banyak karakter */
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            width: 297mm;
            /* A4 landscape width */
            height: 210mm;
            /* A4 landscape height */
            position: relative;
            overflow: hidden;
        }

        .certificate-container {
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
        }

        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .content-wrapper {
            position: absolute;
            top: 120mm;
            left: 125mm;
            transform: translate(-50%, -50%);
            text-align: left;
            width: 85%;
            max-width: 800px;
        }

        .event-card {
            max-width: 80%;
        }

        /* Gaya untuk nama (Judul Utama) */
        .speaker-name {
            font-size: 36px;
            /* Larger for landscape */
            font-weight: bold;
            margin: 0;
            padding-bottom: 0px;

            background: linear-gradient(90deg, #f83292 0%, #20c997 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            color: #f83292;
            /* fallback for browsers that don't support gradient text */

            /* Garis bawah tebal */
            border-bottom: 4px solid #6c757d;
            margin-bottom: 25px;
        }

        /* Teks "Sebagai Narasumber..." */
        .role-text {
            font-size: 16px;
            color: #000000;
            line-height: 1;
        }

        /* Sorotan pada kata "Narasumber" */
        .role-text .highlight {
            background-color: #20c997;
            color: #ffffff;
            padding: 2px 10px;
            font-weight: bold;
            border-radius: 8px;
            line-height: 1;
        }

        /* Judul Workshop */
        .workshop-title {
            font-size: 24px;
            font-weight: bold;
            color: #005f73;
            line-height: 1;
            margin-top: -10px;
            text-transform: uppercase;
        }

        /* Detail acara */
        .event-details {
            font-size: 16px;
            line-height: 1;
            color: #000000;
            margin-top: -20px;
            line-height: 1;
        }

        /* Signature section */
        .signature {
            position: absolute;
            bottom: 40mm;
            right: 50mm;
            text-align: center;
        }

        .signature p {
            margin: 8px 0;
            font-size: 16px;
            color: #495057;
        }

        .signature img {
            height: 70px;
            margin: 15px 0;
        }

        .signature strong {
            font-size: 18px;
            color: #005f73;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="background-image">
            <img src="{{ public_path('ext_images/template_sertifikat.png') }}"
                style="width: 100%; height: 100%; object-fit: cover;" alt="Certificate Background">
        </div>

        <div class="content-wrapper">
            <div class="event-card">
                <h1 class="speaker-name">{{ $participant_name }}</h1>
                <p class="role-text">
                    Sebagai <span class="highlight">Peserta</span> dalam Kegiatan
                </p>
                <h2 class="workshop-title">
                    {{ $event_name }}
                </h2>
                @php
                    $dates = explode(' - ', $event_date);
                    $before = $dates[0] ?? null;
                    $after = $dates[1] ?? null;
                    \Carbon\Carbon::setLocale('id');
                    $date_before = $before ? \Carbon\Carbon::parse($before)->translatedFormat('l, d F Y') : null;
                    $date_after = $after ? \Carbon\Carbon::parse($after)->translatedFormat('l, d F Y') : null;
                    // dd($date_before, $date_after);
                @endphp

                <p class="event-details">
                    dilaksanakan oleh Rumah Jurnal UIN Sjech M. Djamil Djambek Bukittinggi <br>pada
                    {{ $date_before }}
                </p>
            </div>
        </div>

        {{-- <div class="signature">
            <p>Bukittinggi, {{ $date }}</p>
            <p>Editor in Chief,</p>
            <img src="{{ $chief_editor_signature }}" alt="Signature">
            <p><strong>{{ $chief_editor }}</strong></p>
        </div> --}}
    </div>
</body>

</html>
