<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - {{ $setting_web->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #15365F;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background-color: #C3A356;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            padding: 20px;
            background-color: #e9e9e9;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 5px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $setting_web->name }}</h1>
        <h2>Reset Password Request</h2>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $user->name }}</strong>,</p>

        <p>Kami menerima permintaan untuk mereset password akun Anda. Jika Anda tidak melakukan permintaan ini, Anda dapat mengabaikan email ini.</p>

        <p>Untuk mereset password Anda, silakan klik tombol di bawah ini:</p>

        <p style="text-align: center;">
            <a href="{{ $resetLink }}" class="button">Reset Password</a>
        </p>

        <p>Atau salin dan tempel link berikut ke browser Anda:</p>
        <p style="word-break: break-all; background-color: #f5f5f5; padding: 10px; border-radius: 3px;">
            {{ $resetLink }}
        </p>

        <div class="warning">
            <strong>Perhatian:</strong>
            <ul>
                <li>Link reset password ini hanya berlaku selama 24 jam</li>
                <li>Jika link sudah kadaluarsa, silakan lakukan permintaan reset password baru</li>
                <li>Untuk keamanan, jangan bagikan link ini kepada siapapun</li>
            </ul>
        </div>

        <p>Jika Anda mengalami kesulitan mengklik tombol "Reset Password", salin dan tempel URL di atas ke browser web Anda.</p>

        <p>Terima kasih,<br>
        Tim {{ $setting_web->name }}</p>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        <p>{{ $setting_web->address }}</p>
        <p>{{ $setting_web->email }} | {{ $setting_web->phone }}</p>
    </div>
</body>
</html>
