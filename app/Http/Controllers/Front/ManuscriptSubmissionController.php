<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\ManuscriptSubmissionReceivedMail;
use App\Mail\NewManuscriptSubmissionEditorMail;
use App\Models\Journal;
use App\Models\SettingWebsite;
use App\Models\User;
use App\Models\WaitingSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class ManuscriptSubmissionController extends Controller
{
    public function create(): View
    {
        $settingWeb = SettingWebsite::first();

        return view('front.pages.manuscript-submission.create', [
            'title' => 'Manuscript Submission',
            'meta' => [
                'title' => 'Manuscript Submission | '.$settingWeb->name,
                'description' => 'Submit a manuscript to one of the journals managed by '.$settingWeb->name.'.',
                'keywords' => $settingWeb->name.', manuscript submission, journal, article, research',
                'favicon' => $settingWeb->favicon,
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home'),
                ],
                [
                    'name' => 'Manuscript Submission',
                    'link' => route('manuscript-submission.create'),
                ],
            ],
            'setting_web' => $settingWeb,
            'journals' => Journal::query()
                ->where('type', 'journal')
                ->orderBy('name')
                ->get(['id', 'name', 'title']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('waiting_submissions', 'email'),
            ],
            'username' => [
                'required',
                'alpha_dash',
                'min:4',
                'max:50',
                Rule::unique('users', 'username'),
                Rule::unique('waiting_submissions', 'username'),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'whatsapp_number' => ['required', 'string', 'max:30', 'regex:/^\+?[0-9][0-9\s\-()]{7,28}$/'],
            'institution' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'orcid_id' => ['nullable', 'string', 'max:50', 'regex:/^(https?:\/\/orcid\.org\/)?\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/i'],
            'scopus_or_scholar_url' => ['nullable', 'string', 'max:500'],

            'target_journal_id' => [
                'required',
                Rule::exists('journals', 'id')->where(fn ($query) => $query->where('type', 'journal')->whereNull('deleted_at')),
            ],
            'article_type' => ['required', Rule::in([
                'research_article',
                'community_service_article',
                'systematic_literature_review',
            ])],
            'article_language' => ['required', 'string', 'max:50'],
            'article_title' => ['required', 'string', 'max:500'],
            'abstract' => ['required', 'string', 'min:100'],
            'keywords' => ['required', 'array', 'min:3', 'max:5'],
            'keywords.*' => ['required', 'string', 'max:100', 'distinct:ignore_case'],
            'reference_list' => ['required', 'string'],
            'correspondence_email' => ['nullable', 'email:rfc', 'max:255'],
            'funding_source' => ['nullable', 'string', 'max:255'],

            'has_international_authors' => ['nullable', 'boolean'],
            'international_authors' => ['exclude_unless:has_international_authors,1', 'required', 'array', 'min:1'],
            'international_authors.*.institution_name' => ['required', 'string', 'max:255'],
            'international_authors.*.department' => ['required', 'string', 'max:255'],
            'international_authors.*.country' => ['required', 'string', 'max:100'],
            'international_authors.*.institutional_email' => ['required', 'email:rfc', 'max:255'],
            'international_authors.*.orcid_or_scopus_id' => ['required', 'string', 'max:255'],
            'international_authors.*.contribution' => ['required', 'string', 'max:1000'],
            'international_authors.*.consent' => ['required', 'accepted'],
            'international_author_confirmation' => ['exclude_unless:has_international_authors,1', 'required', 'accepted'],

            'is_original_work' => ['accepted'],
            'not_previously_published' => ['accepted'],
            'not_under_consideration' => ['accepted'],
            'all_authors_approved' => ['accepted'],
            'authorship_information_correct' => ['accepted'],
            'international_authors_agreed' => ['accepted'],
            'uses_official_template' => ['accepted'],
            'agrees_peer_review' => ['accepted'],
            'agrees_publication_process' => ['accepted'],
            'agrees_publication_fees' => ['accepted'],
        ], [
            'email.unique' => 'This email address is already registered or has an active submission.',
            'username.unique' => 'This username is already registered or reserved by an active submission.',
            'password.confirmed' => 'The password confirmation does not match.',
            'whatsapp_number.regex' => 'Enter a valid WhatsApp number, including the country code.',
            'orcid_id.regex' => 'Enter a valid ORCID iD, for example 0000-0002-1825-0097.',
            'abstract.min' => 'The abstract must contain at least 100 characters.',
            'keywords.min' => 'Enter at least 3 keywords.',
            'keywords.max' => 'Enter no more than 5 keywords.',
            'keywords.*.distinct' => 'Each keyword must be unique.',
            'international_authors.required' => 'Add at least one international author.',
            'international_author_confirmation.required' => 'You must confirm the international author’s consent.',
            '*.accepted' => 'This declaration must be confirmed.',
        ]);

        if ($validator->fails()) {
            Alert::error('Submission incomplete', 'Please review the highlighted fields.');

            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $hasInternationalAuthors = $request->boolean('has_international_authors');

        $declarationFields = [
            'is_original_work',
            'not_previously_published',
            'not_under_consideration',
            'all_authors_approved',
            'authorship_information_correct',
            'international_authors_agreed',
            'uses_official_template',
            'agrees_peer_review',
            'agrees_publication_process',
            'agrees_publication_fees',
        ];

        foreach ($declarationFields as $field) {
            $validated[$field] = $request->boolean($field);
        }

        $validated['submission_code'] = (string) Str::uuid();
        $validated['has_international_authors'] = $hasInternationalAuthors;
        $validated['international_authors'] = $hasInternationalAuthors
            ? array_values($validated['international_authors'])
            : null;
        $validated['international_author_confirmation'] = $hasInternationalAuthors
            && $request->boolean('international_author_confirmation');
        $validated['status'] = 'waiting';
        $validated['submitted_at'] = now();

        unset($validated['password_confirmation']);

        $submission = WaitingSubmission::create($validated);

        try {
            Mail::to($submission->email)->send(new ManuscriptSubmissionReceivedMail($submission));
        } catch (\Throwable $exception) {
            Log::error('Failed to send manuscript submission confirmation email.', [
                'submission_id' => $submission->id,
                'submission_code' => $submission->submission_code,
                'recipient' => $submission->email,
                'exception' => $exception->getMessage(),
            ]);
        }

        $this->notifyJournalEditors($submission);

        Alert::success('Submission received', 'Your manuscript information has been saved successfully.');

        return redirect()->route('manuscript-submission.success', $submission->submission_code);
    }

    public function success(string $submissionCode): View
    {
        $submission = WaitingSubmission::query()
            ->where('submission_code', $submissionCode)
            ->firstOrFail(['submission_code', 'article_title', 'submitted_at']);
        $settingWeb = SettingWebsite::first();

        return view('front.pages.manuscript-submission.success', [
            'title' => 'Submission Received',
            'meta' => [
                'title' => 'Submission Received | '.$settingWeb->name,
                'description' => 'Your manuscript submission has been received.',
                'keywords' => $settingWeb->name.', manuscript submission',
                'favicon' => $settingWeb->favicon,
            ],
            'breadcrumbs' => [
                ['name' => __('front.home'), 'link' => route('home')],
                ['name' => 'Manuscript Submission', 'link' => route('manuscript-submission.create')],
                ['name' => 'Submission Received', 'link' => route('manuscript-submission.success', $submissionCode)],
            ],
            'setting_web' => $settingWeb,
            'submission' => $submission,
        ]);
    }

    private function notifyJournalEditors(WaitingSubmission $submission): void
    {
        $submission->loadMissing('targetJournal');
        $journalPermission = $submission->targetJournal?->url_path;

        if (! $journalPermission) {
            Log::warning('Unable to notify editors because the target journal has no URL path.', [
                'submission_id' => $submission->id,
                'target_journal_id' => $submission->target_journal_id,
            ]);

            return;
        }

        $editors = User::query()
            ->whereHas('roles', function ($roleQuery) {
                $roleQuery->where('name', 'editor')
                    ->where('guard_name', 'web');
            })
            ->whereNotNull('email')
            ->where(function ($query) use ($journalPermission) {
                $permissionFilter = function ($permissionQuery) use ($journalPermission) {
                    $permissionQuery->where('name', $journalPermission)
                        ->where('guard_name', 'web');
                };

                $query->whereHas('permissions', $permissionFilter)
                    ->orWhereHas('roles.permissions', $permissionFilter);
            })
            ->get();

        foreach ($editors as $editor) {
            try {
                Mail::to($editor->email)->send(
                    new NewManuscriptSubmissionEditorMail($submission, $editor)
                );
            } catch (\Throwable $exception) {
                Log::error('Failed to send new manuscript notification to an editor.', [
                    'submission_id' => $submission->id,
                    'submission_code' => $submission->submission_code,
                    'editor_id' => $editor->id,
                    'recipient' => $editor->email,
                    'journal_permission' => $journalPermission,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }
    }
}
