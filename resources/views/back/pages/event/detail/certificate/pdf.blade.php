<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: {{ $template->paper_size }} {{ $template->orientation }};
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            @if ($template->orientation == 'landscape')
                width: 297mm;
                height: 210mm;
            @else
                width: 210mm;
                height: 297mm;
            @endif
            position: relative;
            overflow: hidden;
        }

        .certificate-container {
            @if ($template->orientation == 'landscape')
                width: 297mm;
                height: 210mm;
            @else
                width: 210mm;
                height: 297mm;
            @endif
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

        .background-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .text-element {
            position: absolute;
            white-space: nowrap;
            line-height: 1;
        }

        .signature-element {
            position: absolute;
        }

        .signature-element img {
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    @php
        $textElements = $template->text_elements ?? [];
        $signaturePosition = $template->signature_position ?? ['x' => 125, 'y' => 400];

        // Canvas size in editor (pixels) - A4 at 96 DPI
        // Landscape: 842px x 595px = 297mm x 210mm
        // Portrait: 595px x 842px = 210mm x 297mm
        if ($template->orientation == 'landscape') {
            $canvasWidth = 842; // px
            $canvasHeight = 595; // px
            $pdfWidth = 297; // mm
            $pdfHeight = 210; // mm
        } else {
            $canvasWidth = 595; // px
            $canvasHeight = 842; // px
            $pdfWidth = 210; // mm
            $pdfHeight = 297; // mm
        }

        // Scale factor: convert editor pixels to PDF mm
        $scaleX = $pdfWidth / $canvasWidth;
        $scaleY = $pdfHeight / $canvasHeight;

        // Parse event date
        $dates = explode(' - ', $event_date);
        $before = $dates[0] ?? null;
        $after = $dates[1] ?? null;
        \Carbon\Carbon::setLocale('id');
        $date_before = $before ? \Carbon\Carbon::parse($before)->translatedFormat('l, d F Y') : null;
        $date_after = $after ? \Carbon\Carbon::parse($after)->translatedFormat('l, d F Y') : null;

        // Replacements for placeholders
        $replacements = [
            '{certificate_number}' => 'B-' . ($certificate_number ?? '0000') . '/ln.26/HM.00/' . ($after ? \Carbon\Carbon::parse($after)->format('m') : \Carbon\Carbon::now()->format('m')) . '/' . ($after ? \Carbon\Carbon::parse($after)->format('Y') : \Carbon\Carbon::now()->format('Y')),
            '{participant_name}' => $participant_name ?? 'Nama Peserta',
            '{event_name}' => $event_name ?? 'Nama Event',
            '{event_date}' => $date_before ?? 'Tanggal Event',
            'Nomor: B-0001/ln.26/HM.00/01/2026' => 'Nomor: B-' . ($certificate_number ?? '0000') . '/ln.26/HM.00/' . ($after ? \Carbon\Carbon::parse($after)->format('m') : \Carbon\Carbon::now()->format('m')) . '/' . ($after ? \Carbon\Carbon::parse($after)->format('Y') : \Carbon\Carbon::now()->format('Y')),
        ];
    @endphp

    <div class="certificate-container">
        <div class="background-image">
            @if ($template->template_image)
                <img src="{{ public_path('storage/' . $template->template_image) }}" alt="Certificate Background">
            @else
                <img src="{{ public_path('ext_images/template_sertifikat.png') }}" alt="Certificate Background">
            @endif
        </div>

        @foreach ($textElements as $element)
            @php
                $text = $element['placeholder'] ?? '';

                // Replace placeholders with actual values
                foreach ($replacements as $placeholder => $value) {
                    $text = str_replace($placeholder, $value, $text);
                }

                // Also check for element IDs that should be replaced
                if ($element['id'] === 'participant_name' && strpos($text, '{participant_name}') === false) {
                    $text = $participant_name ?? $text;
                }
                if ($element['id'] === 'event_name' && strpos($text, '{event_name}') === false) {
                    $text = $event_name ?? $text;
                }

                // Convert pixel position to mm using correct scale (no padding offset needed)
                $x = ($element['x'] ?? 0) * $scaleX;
                $y = ($element['y'] ?? 0) * $scaleY;

                // Font size: scale proportionally to canvas size
                // Editor: fontSizePx on 842px canvas
                // PDF: fontSizeMm on 297mm canvas, then convert to pt
                // fontSizeMm = fontSizePx * scaleX
                // fontSizePt = fontSizeMm * 2.8346 (1mm = 2.8346pt)
                // Simplified: fontSizePt = fontSizePx * scaleX * 2.8346 ≈ fontSizePx * 1.0
                // So we use pt unit which equals to px value
                $fontSizePx = $element['fontSize'] ?? 16;
                $fontSizePt = $fontSizePx; // 1px in editor ≈ 1pt in PDF for this scale
            @endphp
            <div class="text-element"
                style="
                    left: {{ $x }}mm;
                    top: {{ $y }}mm;
                    font-size: {{ $fontSizePt }}pt;
                    font-weight: {{ $element['fontWeight'] ?? 'normal' }};
                    color: {{ $element['color'] ?? '#000000' }};
                ">
                {{ $text }}
            </div>
        @endforeach

        @if ($template->signature_image || true)
            @php
                // Convert signature position and size using correct scale
                $sigX = ($signaturePosition['x'] ?? 125) * $scaleX;
                $sigY = ($signaturePosition['y'] ?? 400) * $scaleY;
                $sigWidth = ($signaturePosition['width'] ?? 200) * $scaleX;
            @endphp
            <div class="signature-element" style="left: {{ $sigX }}mm; top: {{ $sigY }}mm; width: {{ $sigWidth }}mm;">
                @if ($template->signature_image)
                    <img src="{{ public_path('storage/' . $template->signature_image) }}" alt="Signature">
                @else
                    <img src="{{ public_path('ext_images/ttd_firdaus.png') }}" alt="Signature">
                @endif
            </div>
        @endif
    </div>
</body>

</html>
