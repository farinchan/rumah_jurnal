<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Mail\ManuscriptSubmissionStatusMail;
use App\Models\Journal;
use App\Models\WaitingSubmission;
use App\Services\MailRecipientService;
use App\Services\WhatsappService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class ManuscriptSubmissionController extends Controller
{
    public function __construct(
        private readonly WhatsappService $whatsappService,
        private readonly MailRecipientService $mailRecipientService
    ) {}

    public function index(Request $request, string $journalPath): View
    {
        $journal = $this->authorizedJournal($request, $journalPath);
        $status = $request->string('status')->toString();
        $search = trim($request->string('search')->toString());

        $baseQuery = $journal->waitingSubmissions();
        $statusCounts = [
            'waiting' => (clone $baseQuery)->where('status', 'waiting')->count(),
            'under_review' => (clone $baseQuery)->where('status', 'under_review')->count(),
            'accepted' => (clone $baseQuery)->where('status', 'accepted')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'total' => (clone $baseQuery)->count(),
        ];

        $submissions = $baseQuery
            ->with('reviewer:id,name')
            ->when(in_array($status, ['waiting', 'under_review', 'accepted', 'rejected'], true),
                fn ($query) => $query->where('status', $status)
            )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('submission_code', 'like', "%{$search}%")
                        ->orWhere('article_title', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('institution', 'like', "%{$search}%");
                });
            })
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        return view('back.pages.manuscript-submissions.index', [
            'title' => 'Manuscript Submissions',
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'link' => route('back.dashboard')],
                ['name' => $journal->name, 'link' => route('back.journal.index', $journal->url_path)],
                ['name' => 'Manuscript Submissions', 'link' => route('back.journal.manuscript-submissions.index', $journal->url_path)],
            ],
            'journal' => $journal,
            'submissions' => $submissions,
            'statusCounts' => $statusCounts,
            'filters' => compact('status', 'search'),
        ]);
    }

    public function show(Request $request, string $journalPath, string $submissionCode): View
    {
        $journal = $this->authorizedJournal($request, $journalPath);
        $submission = $journal->waitingSubmissions()
            ->with('reviewer:id,name,email')
            ->where('submission_code', $submissionCode)
            ->firstOrFail();

        return view('back.pages.manuscript-submissions.show', [
            'title' => 'Submission Detail',
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'link' => route('back.dashboard')],
                ['name' => $journal->name, 'link' => route('back.journal.index', $journal->url_path)],
                ['name' => 'Manuscript Submissions', 'link' => route('back.journal.manuscript-submissions.index', $journal->url_path)],
                ['name' => $submission->submission_code, 'link' => route('back.journal.manuscript-submissions.show', [$journal->url_path, $submission->submission_code])],
            ],
            'journal' => $journal,
            'submission' => $submission,
        ]);
    }

    public function updateStatus(
        Request $request,
        string $journalPath,
        string $submissionCode
    ): RedirectResponse {
        $journal = $this->authorizedJournal($request, $journalPath);
        $submission = $journal->waitingSubmissions()
            ->where('submission_code', $submissionCode)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => ['required', Rule::in(['under_review', 'accepted', 'rejected'])],
            'editor_notes' => ['nullable', 'string', 'max:5000'],
            'rejection_reason' => [
                Rule::requiredIf(fn () => $request->input('status') === 'rejected'),
                'nullable',
                'string',
                'max:5000',
            ],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $statusChanged = $submission->status !== $validated['status'];

        $submission->update([
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'editor_notes' => $validated['editor_notes'] ?? null,
            'rejection_reason' => $validated['status'] === 'rejected'
                ? $validated['rejection_reason']
                : null,
        ]);

        if ($statusChanged) {
            $this->notifyAuthor($submission->fresh(['targetJournal', 'reviewer']));
        }

        Alert::success('Status updated', 'Status manuscript submission berhasil diperbarui.');

        return back();
    }

    private function authorizedJournal(Request $request, string $journalPath): Journal
    {
        $journal = Journal::query()
            ->where('url_path', $journalPath)
            ->where('type', 'journal')
            ->firstOrFail();

        abort_unless($request->user()->can($journal->url_path), 403);

        return $journal;
    }

    private function notifyAuthor(WaitingSubmission $submission): void
    {
        try {
            Mail::to($this->mailRecipientService->resolve($submission->email))
                ->send(new ManuscriptSubmissionStatusMail($submission));
        } catch (\Throwable $exception) {
            Log::error('Failed to send manuscript status email.', [
                'submission_id' => $submission->id,
                'submission_code' => $submission->submission_code,
                'recipient' => $submission->email,
                'exception' => $exception->getMessage(),
            ]);
        }

        try {
            $result = $this->whatsappService->sendMessage(
                $submission->whatsapp_number,
                $this->statusWhatsappMessage($submission)
            );

            if (! ($result['success'] ?? false)) {
                Log::warning('Failed to send manuscript status WhatsApp notification.', [
                    'submission_id' => $submission->id,
                    'submission_code' => $submission->submission_code,
                    'recipient' => $submission->whatsapp_number,
                    'response' => $result,
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to send manuscript status WhatsApp notification.', [
                'submission_id' => $submission->id,
                'submission_code' => $submission->submission_code,
                'recipient' => $submission->whatsapp_number,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    private function statusWhatsappMessage(WaitingSubmission $submission): string
    {
        $statusLabel = match ($submission->status) {
            'under_review' => 'Sedang Ditinjau',
            'accepted' => 'Diterima pada Pemeriksaan Awal',
            'rejected' => 'Tidak Dilanjutkan',
            default => ucfirst($submission->status),
        };

        $message = "Halo Bapak/Ibu {$submission->first_name} {$submission->last_name},\n\n"
            ."Status manuscript submission Anda telah diperbarui.\n\n"
            ."Kode Submission: *{$submission->submission_code}*\n"
            ."Judul: {$submission->article_title}\n"
            ."Jurnal: {$submission->targetJournal?->name}\n"
            ."Status: *{$statusLabel}*\n";

        if ($submission->status === 'rejected' && $submission->rejection_reason) {
            $message .= "\nAlasan:\n{$submission->rejection_reason}\n";
        }

        return $message."\nSilakan periksa email Anda untuk informasi lebih lanjut.\n\n"
            ."Salam,\nRumah Jurnal UIN Sjech M. Djamil Djambek Bukittinggi\n\n"
            ."_Pesan ini dikirim otomatis oleh sistem_\n".url('/');
    }
}
