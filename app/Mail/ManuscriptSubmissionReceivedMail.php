<?php

namespace App\Mail;

use App\Models\WaitingSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ManuscriptSubmissionReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WaitingSubmission $submission)
    {
        $this->submission->loadMissing('targetJournal');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Manuscript Submission Received - '.$this->submission->submission_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.manuscript_submission_received',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
