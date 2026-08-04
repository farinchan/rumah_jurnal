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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;
use ResourceBundle;
use RuntimeException;

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
        $ojsCredentials = null;
        $accountAlreadyExists = false;
        $ojsResponseData = null;

        if (
            $validated['status'] === 'accepted'
            && $statusChanged
            && ! $submission->ojs_account_created_at
        ) {
            try {
                $password = $this->resolveOjsPassword($submission);
                $countryCode = $this->countryCode($submission->country);
                $endpoint = rtrim($journal->url, '/').'/api/v1/users';

                $response = Http::acceptJson()
                    ->withToken($journal->api_key)
                    ->withOptions([
                        'allow_redirects' => [
                            'strict' => true,
                            'max' => 10,
                        ],
                    ])
                    ->timeout(60)
                    ->post($endpoint, [
                        'username' => $submission->username,
                        'password' => $password,
                        'email' => $submission->email,
                        'givenName' => $submission->first_name,
                        'familyName' => $submission->last_name,
                        'locale' => $countryCode === 'ID' ? 'id_ID' : 'en_US',
                        'country' => $countryCode,
                        'phone' => $submission->whatsapp_number,
                        'affiliation' => $submission->institution,
                        'mustChangePassword' => true,
                    ]);

                if (! $response->successful()) {
                    $errorMessage = (string) ($response->json('errorMessage')
                        ?? $response->json('message')
                        ?? $response->json('error')
                        ?? "OJS returned HTTP {$response->status()}.");

                    if ($this->isEmailAlreadyInUseError($errorMessage)) {
                        Log::info('OJS account creation skipped because email is already in use.', [
                            'submission_id' => $submission->id,
                            'submission_code' => $submission->submission_code,
                            'email' => $submission->email,
                            'errorMessage' => $errorMessage,
                        ]);

                        $accountAlreadyExists = true;
                        $ojsResponseData = [
                            'status' => 'already_exists',
                            'message' => $errorMessage,
                        ];
                    } else {
                        throw new RuntimeException($errorMessage);
                    }
                } else {
                    $responseData = $response->json();
                    $ojsUserId = (string) ($responseData['id'] ?? $responseData['userId'] ?? '');

                    $ojsCredentials = [
                        'user_id' => $ojsUserId,
                        'username' => $submission->username,
                        'password' => $password,
                        'login_url' => rtrim($journal->url, '/'),
                        'response_data' => $responseData,
                    ];
                }

            } catch (\Throwable $exception) {
                Log::error('Failed to create an OJS account for an accepted manuscript.', [
                    'submission_id' => $submission->id,
                    'submission_code' => $submission->submission_code,
                    'journal_id' => $journal->id,
                    'journal_url' => $journal->url,
                    'exception' => $exception->getMessage(),
                ]);

                Alert::error('OJS account failed', 'Akun OJS gagal dibuat. Status submission belum diubah.');

                return back()
                    ->withErrors(['ojs_account' => $exception->getMessage()])
                    ->withInput();
            }
        }

        $updates = [
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'editor_notes' => $validated['editor_notes'] ?? null,
            'rejection_reason' => $validated['status'] === 'rejected'
                ? $validated['rejection_reason']
                : null,
        ];

        if ($ojsCredentials) {
            $updates['ojs_user_id'] = $ojsCredentials['user_id'];
            $updates['ojs_account_created_at'] = now();
            $updates['ojs_response'] = $ojsCredentials['response_data'] ?? null;
        } elseif ($accountAlreadyExists) {
            $updates['ojs_account_created_at'] = now();
            $updates['ojs_response'] = $ojsResponseData ?? null;
        }

        $submission->update($updates);

        if ($statusChanged) {
            $this->notifyAuthor(
                $submission->fresh(['targetJournal', 'reviewer']),
                $ojsCredentials
            );
        }

        $successMessage = $accountAlreadyExists
            ? 'Status manuscript submission berhasil diperbarui. (Email sudah terdaftar di OJS).'
            : 'Status manuscript submission berhasil diperbarui.';

        Alert::success('Status updated', $successMessage);

        return back();
    }

    private function authorizedJournal(Request $request, string $journalPath): Journal
    {
        $journal = Journal::query()
            ->where('url_path', $journalPath)
            ->firstOrFail();

        abort_unless($request->user()->can($journal->url_path), 403);

        return $journal;
    }

    private function notifyAuthor(
        WaitingSubmission $submission,
        ?array $ojsCredentials = null
    ): void {
        $mailCredentials = $ojsCredentials
            ? [
                'username' => $ojsCredentials['username'],
                'password' => $ojsCredentials['password'],
                'login_url' => $ojsCredentials['login_url'],
            ]
            : null;

        try {
            Mail::to($this->mailRecipientService->resolve($submission->email))
                ->send(new ManuscriptSubmissionStatusMail($submission, $mailCredentials));
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
                $this->statusWhatsappMessage($submission, $mailCredentials)
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

    private function statusWhatsappMessage(
        WaitingSubmission $submission,
        ?array $ojsCredentials = null
    ): string {
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

        if ($submission->status === 'accepted') {
            if ($ojsCredentials) {
                $message .= "\n*Akun OJS Anda*\n"
                    ."Username: {$ojsCredentials['username']}\n"
                    ."Password sementara: {$ojsCredentials['password']}\n"
                    ."Login: {$ojsCredentials['login_url']}\n\n"
                    ."Anda wajib mengganti password setelah login pertama.\n";
            } else {
                $loginUrl = rtrim($submission->targetJournal?->url ?? url('/'), '/');
                $message .= "\n*Catatan Akun OJS*\n"
                    ."Email Anda (*{$submission->email}*) sudah terdaftar di sistem OJS jurnal kami.\n"
                    ."Silakan login menggunakan akun OJS yang telah Anda miliki sebelumnya melalui tautan:\n"
                    ."{$loginUrl}\n\n"
                    ."Jika Anda lupa password, silakan gunakan fitur \"Lupa Password\" pada halaman login website OJS.\n";
            }
        }

        return $message."\nSilakan periksa email Anda untuk informasi lebih lanjut.\n\n"
            ."Salam,\nRumah Jurnal UIN Sjech M. Djamil Djambek Bukittinggi\n\n"
            ."_Pesan ini dikirim otomatis oleh sistem_\n".url('/');
    }

    private function isEmailAlreadyInUseError(string $errorMessage): bool
    {
        $normalized = Str::lower($errorMessage);

        return Str::contains($normalized, ['email', 'username', 'user'])
            && Str::contains($normalized, ['already', 'use', 'exist', 'registered', 'duplicate', 'taken']);
    }


    private function resolveOjsPassword(WaitingSubmission $submission): string
    {
        try {
            return Crypt::decryptString($submission->password);
        } catch (\Throwable) {
            $temporaryPassword = Str::password(16);

            $submission->forceFill([
                'password' => Crypt::encryptString($temporaryPassword),
            ])->save();

            return $temporaryPassword;
        }
    }

    private function countryCode(string $country): string
    {
        $country = trim($country);

        if (strlen($country) === 2) {
            return strtoupper($country);
        }

        $aliases = [
            'indonesia' => 'ID',
            'malaysia' => 'MY',
            'singapura' => 'SG',
            'brunei darussalam' => 'BN',
            'timor leste' => 'TL',
        ];
        $normalizedCountry = Str::lower($country);

        if (isset($aliases[$normalizedCountry])) {
            return $aliases[$normalizedCountry];
        }

        return 'ID';
    }
}
