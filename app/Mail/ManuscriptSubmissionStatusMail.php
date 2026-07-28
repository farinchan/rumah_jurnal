<?php

namespace App\Mail;

use App\Models\WaitingSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ManuscriptSubmissionStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WaitingSubmission $submission,
        public ?array $ojsCredentials = null
    ) {
        $this->submission->loadMissing(['targetJournal', 'reviewer']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Manuscript Submission Update - '.$this->submission->submission_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.manuscript_submission_status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
