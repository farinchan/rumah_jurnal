<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manuscript Submission Received</title>
</head>
<body style="background:#f2f4f7; color:#344054; font-family:Arial,Helvetica,sans-serif; margin:0; padding:32px 16px;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                    style="background:#ffffff; border:1px solid #e4e7ec; border-radius:12px; max-width:640px; overflow:hidden;">
                    <tr>
                        <td style="border-bottom:1px solid #e4e7ec; padding:28px 36px;">
                            <p style="color:#c3a356; font-size:12px; font-weight:700; letter-spacing:.1em; margin:0 0 8px; text-transform:uppercase;">
                                Rumah Jurnal
                            </p>
                            <h1 style="color:#15365f; font-size:24px; line-height:1.3; margin:0;">
                                Manuscript Submission Received
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 36px;">
                            <p style="line-height:1.7; margin:0 0 18px;">
                                Dear {{ $submission->first_name }} {{ $submission->last_name }},
                            </p>

                            <p style="line-height:1.7; margin:0 0 22px;">
                                Thank you for submitting your manuscript information to
                                <strong>Rumah Jurnal UIN Sjech M. Djamil Djambek Bukittinggi</strong>.
                                Your submission has been received and is waiting for preliminary editorial review.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="background:#f8fafc; border:1px solid #e4e7ec; border-radius:8px; margin-bottom:24px;">
                                <tr>
                                    <td style="color:#667085; padding:16px 18px 6px;">Submission reference</td>
                                </tr>
                                <tr>
                                    <td style="color:#15365f; font-family:monospace; font-size:15px; font-weight:700; padding:0 18px 16px; overflow-wrap:anywhere;">
                                        {{ $submission->submission_code }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px solid #e4e7ec; padding:14px 18px;">
                                        <strong>Article title:</strong><br>
                                        {{ $submission->article_title }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px solid #e4e7ec; padding:14px 18px;">
                                        <strong>Target journal:</strong><br>
                                        {{ $submission->targetJournal?->name ?? $submission->targetJournal?->title ?? '—' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px solid #e4e7ec; padding:14px 18px;">
                                        <strong>Status:</strong> Waiting for preliminary editorial review
                                    </td>
                                </tr>
                            </table>

                            <p style="line-height:1.7; margin:0 0 18px;">
                                If the manuscript passes the preliminary editorial review, the editor will create
                                a username and password for the journal’s online system. You will then be asked to
                                complete the formal submission through
                                <a href="https://ejournal.uinbukittinggi.ac.id/"
                                    style="color:#15365f; font-weight:600;">https://ejournal.uinbukittinggi.ac.id/</a>.
                            </p>

                            <p style="line-height:1.7; margin:0 0 18px;">
                                Please keep the submission reference above for future communication. This email only
                                confirms receipt of the form and does not constitute acceptance for publication.
                            </p>

                            <p style="line-height:1.7; margin:26px 0 0;">
                                Sincerely,<br>
                                <strong>Rumah Jurnal Editorial Team</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc; border-top:1px solid #e4e7ec; color:#98a2b3; font-size:12px; line-height:1.6; padding:18px 36px; text-align:center;">
                            This is an automated notification. Please do not reply to this email.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
