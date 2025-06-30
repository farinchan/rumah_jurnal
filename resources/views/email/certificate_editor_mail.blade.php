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
                                    Dear {{ $mailData['name'] }} <br>
                                    {{ $mailData['affiliation'] }},
                                    <br>
                                    {{-- <span style="color:#3F4254; font-weight:200; font-size:14px;">
                                        {{ $mailData['affiliation'] }}
                                    </span> --}}
                                    <br>
                                </p>

                                <p style="margin-bottom:2px; color:#3F4254; line-height:1.6">
                                    Thank you for your valuable contribution to our journal. Your support and dedication
                                    as a editor are greatly appreciated. We are grateful for your time and effort in
                                    helping us maintain the quality and integrity of our publication.

                                    <br>
                                    <br>

                                    Please find the attached certificate of appreciation for your reference. This
                                    certificate pertains to the journal <strong>"{{ $mailData['journal'] }}"</strong>  and the edition
                                    <strong>"{{ $mailData['edition'] }}"</strong> .
                                    <br>
                                    <br>
                                    Your expertise and insights play a crucial role in the success of our journal. Thank
                                    you for being an integral part of our journey.

                                    <br>
                                    <br>

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
