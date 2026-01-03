<style>
    html,
    body {
        padding: 0;
        margin: 0;
        font-family: Inter, Helvetica, "sans-serif";
    }

    a:hover {
        color: #009ef7;
    }
</style>
<div id="#kt_app_body_content"
    style="background-color:#D5D9E2; font-family:Arial,Helvetica,sans-serif; line-height: 1.5; min-height: 100%; font-weight: normal; font-size: 15px; color: #2F3044; margin:0; padding:0; width:100%; display:flex; justify-content:center; align-items:center">

    <div
        style="background-color:#ffffff; padding: 45px 0 34px 0; border-radius: 24px; margin:40px auto; max-width: 700px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" height="auto"
            style="border-collapse:collapse">
            <tbody>
                <tr>
                    <td align="center" valign="center" style="text-align:center; padding-bottom: 10px">
                        <div style="text-align:left; margin-bottom:54px">
                            <div style="margin:0 60px 55px 60px">
                                <a href="{{ route('home') }}" rel="noopener" target="_blank">
                                    <img alt="Logo"
                                        src="https://rumahjurnal.uinbukittinggi.ac.id/storage/setting/logo.png"
                                        style="height: 35px" />
                                </a>
                            </div>
                            <div
                                style="font-size: 14px; font-weight: 500; margin:0 60px 30px 60px; font-family:Arial,Helvetica,sans-serif">
                                <p
                                    style="color:#181C32; font-size: 16px; font-weight:700; line-height:1.2; margin-bottom:24px">
                                    Halo {{ $mailData['name'] }},
                                    <br>
                                </p>

                                <p style="margin-bottom:2px; color:#3F4254; line-height:1.6">
                                    Anda telah meminta kode verifikasi untuk masuk ke akun Anda. Berikut adalah kode verifikasi 2FA Anda:
                                </p>

                                <div style="text-align: center; margin: 30px 0;">
                                    <div style="background-color: #f8f9fa; border-radius: 12px; padding: 20px; display: inline-block;">
                                        <span style="font-size: 36px; font-weight: 700; letter-spacing: 8px; color: #15365F;">
                                            {{ $mailData['code'] }}
                                        </span>
                                    </div>
                                </div>

                                <p style="margin-bottom:2px; color:#3F4254; line-height:1.6">
                                    Kode ini akan berlaku selama <strong>10 menit</strong>. Jangan bagikan kode ini kepada siapapun.
                                </p>

                                <p style="margin-top:20px; color:#3F4254; line-height:1.6">
                                    Jika Anda tidak meminta kode ini, abaikan email ini atau hubungi tim support kami.
                                </p>

                                <p style="margin-top:30px; color:#3F4254; line-height:1.6">
                                    Terima kasih,<br>
                                    <strong>Tim Rumah Jurnal</strong>
                                </p>
                            </div>
                        </div>

                        <div style="margin:0 60px; border-top: 1px solid #E4E6EF; padding-top: 20px;">
                            <p style="font-size: 12px; color: #B5B5C3; margin: 0;">
                                Email ini dikirim secara otomatis oleh sistem. Mohon untuk tidak membalas email ini.
                            </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
