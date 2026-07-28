<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manuscript Submission Update</title>
</head>
<body style="background:#f2f4f7;color:#344054;font-family:Arial,Helvetica,sans-serif;margin:0;padding:32px 16px;">
    @php
        $statusLabel = match ($submission->status) {
            'under_review' => 'Under Editorial Review',
            'accepted' => 'Passed Preliminary Editorial Review',
            'rejected' => 'Not Proceeding to Formal Submission',
            default => str($submission->status)->replace('_', ' ')->title(),
        };
        $statusColor = match ($submission->status) {
            'accepted' => '#027a48',
            'rejected' => '#b42318',
            default => '#b54708',
        };
    @endphp
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                    style="background:#fff;border:1px solid #e4e7ec;border-radius:12px;max-width:640px;overflow:hidden;">
                    <tr>
                        <td style="border-bottom:1px solid #e4e7ec;padding:28px 36px;">
                            <p style="color:#c3a356;font-size:12px;font-weight:700;letter-spacing:.1em;margin:0 0 8px;text-transform:uppercase;">
                                Rumah Jurnal
                            </p>
                            <h1 style="color:#15365f;font-size:24px;line-height:1.3;margin:0;">
                                Manuscript Submission Update
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 36px;">
                            <p style="line-height:1.7;margin:0 0 18px;">
                                Dear {{ $submission->first_name }} {{ $submission->last_name }},
                            </p>
                            <p style="line-height:1.7;margin:0 0 22px;">
                                The preliminary editorial status of your manuscript has been updated.
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="background:#f8fafc;border:1px solid #e4e7ec;border-radius:8px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <strong>Submission reference:</strong><br>{{ $submission->submission_code }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px solid #e4e7ec;padding:14px 18px;">
                                        <strong>Article title:</strong><br>{{ $submission->article_title }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px solid #e4e7ec;padding:14px 18px;">
                                        <strong>Target journal:</strong><br>{{ $submission->targetJournal?->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px solid #e4e7ec;padding:14px 18px;">
                                        <strong>Status:</strong>
                                        <span style="color:{{ $statusColor }};font-weight:700;">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                                @if ($submission->status === 'rejected' && $submission->rejection_reason)
                                    <tr>
                                        <td style="border-top:1px solid #e4e7ec;padding:14px 18px;">
                                            <strong>Editorial reason:</strong><br>
                                            {!! nl2br(e($submission->rejection_reason)) !!}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                            @if ($submission->status === 'accepted')
                                <p style="line-height:1.7;margin:0 0 18px;">
                                    Your manuscript may proceed to formal submission.
                                </p>
                                @if ($ojsCredentials)
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                        style="background:#ecfdf3;border:1px solid #abefc6;border-radius:8px;margin-bottom:24px;">
                                        <tr>
                                            <td style="padding:16px 18px;">
                                                <strong>Your OJS account</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border-top:1px solid #abefc6;padding:14px 18px;">
                                                <strong>Username:</strong> {{ $ojsCredentials['username'] }}<br>
                                                <strong>Temporary password:</strong> {{ $ojsCredentials['password'] }}<br>
                                                <strong>Login:</strong>
                                                <a href="{{ $ojsCredentials['login_url'] }}">
                                                    {{ $ojsCredentials['login_url'] }}
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="line-height:1.7;margin:0 0 18px;">
                                        You must change the temporary password after your first login, then complete
                                        the formal manuscript submission through the journal’s online system.
                                    </p>
                                @else
                                    <p style="line-height:1.7;margin:0 0 18px;">
                                        Your OJS account has already been provisioned. Please use the credentials
                                        previously sent to you or contact the editorial team if you need assistance.
                                    </p>
                                @endif
                            @elseif ($submission->status === 'under_review')
                                <p style="line-height:1.7;margin:0 0 18px;">
                                    The editorial team is reviewing the information and article metadata you submitted.
                                    We will notify you when the review is complete.
                                </p>
                            @else
                                <p style="line-height:1.7;margin:0 0 18px;">
                                    This decision applies to the preliminary submission form and does not constitute a
                                    formal peer-review decision.
                                </p>
                            @endif
                            <p style="line-height:1.7;margin:26px 0 0;">
                                Sincerely,<br><strong>Rumah Jurnal Editorial Team</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
