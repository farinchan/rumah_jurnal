<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Manuscript Submission</title>
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
                                Editor Notification
                            </p>
                            <h1 style="color:#15365f; font-size:24px; line-height:1.3; margin:0;">
                                New Manuscript Submission
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 36px;">
                            <p style="line-height:1.7; margin:0 0 18px;">
                                Dear {{ $editor->name }},
                            </p>

                            <p style="line-height:1.7; margin:0 0 22px;">
                                A new manuscript has been submitted for preliminary editorial review in
                                <strong>{{ $submission->targetJournal?->name ?? $submission->targetJournal?->title }}</strong>.
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
                                        <strong>Corresponding author:</strong><br>
                                        {{ $submission->first_name }} {{ $submission->last_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px solid #e4e7ec; padding:14px 18px;">
                                        <strong>Author email:</strong><br>
                                        <a href="mailto:{{ $submission->email }}" style="color:#15365f;">
                                            {{ $submission->email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px solid #e4e7ec; padding:14px 18px;">
                                        <strong>Institution:</strong><br>
                                        {{ $submission->institution }}, {{ $submission->country }}
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
                                        <strong>Article type:</strong><br>
                                        {{ str($submission->article_type)->replace('_', ' ')->title() }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px solid #e4e7ec; padding:14px 18px;">
                                        <strong>Submitted at:</strong><br>
                                        {{ $submission->submitted_at?->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            </table>

                            <p style="line-height:1.7; margin:0 0 18px;">
                                Please review the submitted information and follow the preliminary editorial
                                screening process for this journal.
                            </p>

                            <p style="line-height:1.7; margin:26px 0 0;">
                                Sincerely,<br>
                                <strong>Rumah Jurnal System</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc; border-top:1px solid #e4e7ec; color:#98a2b3; font-size:12px; line-height:1.6; padding:18px 36px; text-align:center;">
                            You received this email because you are assigned as an editor for this journal.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
