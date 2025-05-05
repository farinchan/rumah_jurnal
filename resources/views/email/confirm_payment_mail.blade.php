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
                                    Dear {{ $mailData['authorString'] }}
                                    <br>
                                    {{-- <span style="color:#3F4254; font-weight:200; font-size:14px;">
                                        {{ $mailData['affiliation'] }}
                                    </span> --}}
                                    <br>
                                </p>

                                <p style="margin-bottom:2px; color:#3F4254; line-height:1.6">
                                    Thank you for paying the journal publication fees. you have made a payment in the
                                    name of {{ $mailData['payment_account_name']  }} an amount @money($mailData['payment_amount']) on
                                    {{ $mailData['payment_timestamp'] }}.

                                    <br>
                                    <br>
                                    Thank you for your attention and cooperation. We greatly appreciate your
                                    contribution to our journal.

                                    <br>
                                    <br>

                                    Sincerely,
                                    <br>
                                    Editorial Team of {{ $mailData['journal'] }}
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="center"
                        style="font-size: 13px; text-align:center; padding: 0 10px 10px 10px; font-weight: 500; color: #A1A5B7; font-family:Arial,Helvetica,sans-serif">
                        <p style="color:#181C32; font-size: 16px; font-weight: 600; margin-bottom:9px">
                            {{ $mailData['setting_web']['name'] }}
                        </p>
                        <p style="margin-bottom:2px; ">
                            {{ $mailData['setting_web']['address'] }}
                        </p>

                    </td>
                </tr>
                {{-- <tr>
                    <td align="center" valign="center"
                        style="font-size: 13px; padding:0 15px; text-align:center; font-weight: 500; color: #A1A5B7;font-family:Arial,Helvetica,sans-serif">
                        <p>&copy; Copyright {{ $mailData['setting_web->name']  }}.
                            <a href="{{ route("home") }}" rel="noopener" target="_blank"
                                style="font-weight: 600;font-family:Arial,Helvetica,sans-serif">Unsubscribe</a>&nbsp;
                            from newsletter.
                        </p>
                    </td>
                </tr> --}}
            </tbody>
        </table>
    </div>
</div>
