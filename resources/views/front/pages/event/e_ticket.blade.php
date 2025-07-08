<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket</title>
    <style>
        /* === PERUBAHAN PADA BODY === */
        body {
            background-color: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            color: #333;
            /* Properti display:flex dihapus agar body mengizinkan scrolling horizontal */
        }

        /* === PERUBAHAN PADA TICKET-CONTAINER === */
        .ticket-container {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);

            /* Lebar diatur menjadi nilai tetap, bukan lagi max-width */
            width: 800px;

            /* Margin auto digunakan untuk menengahkan di body */
            margin: 40px auto;

            overflow: hidden;
            position: relative;
        }

        /* Header section with EZTIX.ID logo */
        .ticket-header {
            padding: 15px 25px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ticket-header .logo {
            font-weight: bold;
            font-size: 1.2em;
            color: #1e88e5;
        }

        .ticket-header .print-icon {
            font-size: 1.5em;
            cursor: pointer;
        }

        /* Main content area */
        .ticket-body {
            padding: 25px;
        }

        /* Event Details Section */
        .event-details {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .event-info {
            flex-grow: 1;
        }

        .event-info h2 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 1.3em;
        }

        .event-info .social {
            color: #555;
            margin-bottom: 15px;
        }

        .event-info p {
            margin: 5px 0;
            color: #333;
            font-weight: 500;
        }

        .separator {
            position: relative;
            border-top: 2px dashed #cccccc;
            margin: 45px 0;
        }

        .separator::before,
        .separator::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            background-color: #f0f2f5;
            border-radius: 50%;
            top: -21px;
        }

        .separator::before {
            left: -45px;
        }

        .separator::after {
            right: -45px;
        }

        /* Participant Information Section */
        .participant-details {
            display: flex;
            margin-top: 20px;
        }

        .info-left {
            flex-grow: 1;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item .label {
            color: #666;
        }

        .info-item .value {
            font-weight: 600;
            text-align: right;
        }

        .qr-code {
            padding-left: 25px;
            text-align: center;
        }

        .qr-code img {
            width: 140px;
            height: 140px;
            display: block;
        }

        .qr-code .code {
            font-weight: bold;
            margin-top: 8px;
            font-family: 'Courier New', Courier, monospace;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>

<body>

    <div class="ticket-container">
        <div class="ticket-header">
            <img src="{{ asset('storage/setting/logo.png') }}" alt="Rumah Jurnal" style="height: 40px;">
            {{-- <span class="e-ticket-label">E-Ticket</span> --}}
            <span class="print-icon" onclick="window.print()" title="Print Ticket">🖨️</span>
        </div>

        <div class="ticket-body">
            <div class="event-details">
                <div class="event-info">
                    <h2>{{ $eventUser->event->name }}</h2>
                    <p class="social">{{ $eventUser->event->type }} - {{ $eventUser->event->status }}</p>
                    <p><strong>{{ $eventUser->event->datetime }}</strong></p>
                    <p>
                        @if ($eventUser->event->status === 'offline')
                            Lokasi: {{ $eventUser->event->location }}
                        @else
                            Link Meet: <a href="{{ $eventUser->event->location }}"
                                target="_blank">{{ $eventUser->event->location }}</a>
                        @endif
                    </p>
                </div>
            </div>

            <div class="separator"></div>

            <div class="participant-details">
                <div class="info-left">
                    <div class="info-item">
                        <span class="label">Ticket ID.</span>
                        <span class="value">{{ $eventUser->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Name</span>
                        <span class="value">{{ $eventUser->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Email</span>
                        <span class="value">{{ $eventUser->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Phone</span>
                        <span class="value">{{ $eventUser->phone }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Registration Date</span>
                        <span class="value">{{ $eventUser->created_at->format('d M Y H:i') }}</span>
                    </div>
                </div>

                <div class="qr-code">
                    <canvas id="qr_code" class="mx-auto" style="width: 100px; height: 200px;"></canvas>

                </div>
            </div>
        </div>
    </div>

</body>
<script>
    const canvas = document.getElementById('qr_code');
    const ctx = canvas.getContext('2d');
    QRCode.toCanvas(canvas, '{{ $eventUser->id }}', {
        width: 200,
        height: 200,
        margin: 1,
        color: {
            dark: '#000000',
            light: '#ffffff'
        }
    }, function(error) {
        if (error) console.error(error);
        console.log('QR code generated!');
    });
    const logo = new Image();
    logo.src = 'https://rumahjurnal.uinbukittinggi.ac.id/storage/setting/favicon.png';
    logo.onload = function() {
        const logoSize = 50; // Ukuran logo
        const x = (canvas.width - logoSize) / 2; // Posisi X untuk logo
        const y = (canvas.height - logoSize) / 2; // Posisi Y untuk logo
        ctx.drawImage(logo, x, y, logoSize, logoSize); // Gambar logo di tengah QR code
    };
</script>

<script>
    window.onload = function() {
        window.print();
    };
</script>

</html>
