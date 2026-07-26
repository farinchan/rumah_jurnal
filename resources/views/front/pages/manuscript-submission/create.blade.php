@extends('front.app')

@section('seo')
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M. Djamil Djambek Bukittinggi">
    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('manuscript-submission.create') }}">
    <link rel="canonical" href="{{ route('manuscript-submission.create') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('styles')
    <style>
        input[type="text"],
        input[type="email"],
        input[type="datetime-local"],
        input[type="password"],
        input[type="tel"],
        input[type="number"],
        input[type="submit"],
        textarea,
        select {
            margin-bottom: 0;
            height: 50px;
        }

        .submission-shell {
            max-width: 1040px;
            margin: 0 auto;
        }

        .submission-card {
            border: 1px solid #e9edf3;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(21, 54, 95, .08);
            overflow: hidden;
        }

        .submission-header {
            background: #fff;
            border-bottom: 1px solid #e9edf3;
            padding: 32px 40px 24px;
        }

        .submission-header h2 {
            color: #15365f;
            font-size: 28px;
            margin-bottom: 7px;
        }

        .submission-header p {
            color: #758091;
            font-size: 14px;
        }

        .submission-header-label {
            color: #c3a356;
            display: block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .12em;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .submission-introduction {
            background: #f8fafc;
            border-bottom: 1px solid #e9edf3;
            padding: 32px 40px;
        }

        .submission-introduction h3 {
            color: #15365f;
            margin-bottom: 18px;
        }

        .submission-introduction p {
            color: #4f5e70;
            line-height: 1.8;
            margin-bottom: 14px;
        }

        .submission-introduction p:last-child {
            margin-bottom: 0;
        }

        .submission-introduction a {
            color: #15365f;
            font-weight: 600;
            overflow-wrap: anywhere;
            text-decoration: underline;
        }

        .submission-progress {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            padding: 28px 40px 10px;
        }

        .progress-item {
            color: #7b8492;
            font-size: 13px;
            font-weight: 600;
        }

        .progress-item span {
            align-items: center;
            background: #eef2f6;
            border-radius: 50%;
            display: inline-flex;
            height: 34px;
            justify-content: center;
            margin-right: 7px;
            width: 34px;
        }

        .progress-item::after {
            background: #e5e9ee;
            border-radius: 4px;
            content: '';
            display: block;
            height: 4px;
            margin-top: 10px;
        }

        .progress-item.active,
        .progress-item.completed {
            color: #15365f;
        }

        .progress-item.active span,
        .progress-item.completed span {
            background: #c3a356;
            color: #fff;
        }

        .progress-item.active::after,
        .progress-item.completed::after {
            background: #c3a356;
        }

        .submission-step {
            display: none;
            padding: 30px 40px 38px;
        }

        .submission-step.active {
            display: block;
        }

        .step-title {
            border-bottom: 1px solid #edf0f4;
            margin-bottom: 26px;
            padding-bottom: 16px;
        }

        .form-label {
            color: #26364a;
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .required-mark {
            color: #dc3545;
        }

        .form-control,
        .form-select {
            background: #fff;
            border: 1px solid #dce2e9;
            border-radius: 8px;
            height: 52px;
            margin-bottom: 5px;
            padding: 10px 14px;
            width: 100%;
        }

        textarea.form-control {
            height: auto;
            min-height: 130px;
            resize: vertical;
        }

        .field-wrap {
            margin-bottom: 20px;
        }

        .field-help {
            color: #758091;
            display: block;
            font-size: 12px;
            margin-top: 5px;
        }

        .field-error {
            color: #dc3545;
            display: block;
            font-size: 13px;
            margin-top: 5px;
        }

        .keyword-row {
            align-items: center;
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .keyword-row .form-control {
            margin-bottom: 0;
        }

        .international-toggle,
        .declaration-item,
        .consent-item {
            align-items: flex-start;
            background: #f8fafc;
            border: 1px solid #e5eaf0;
            border-radius: 9px;
            display: flex;
            gap: 10px;
            margin-bottom: 11px;
            padding: 14px 16px;
        }

        .international-toggle input,
        .declaration-item input,
        .consent-item input {
            flex: 0 0 auto;
            height: 18px;
            margin-top: 3px;
            width: 18px;
        }

        .international-author-card {
            background: #fbfcfe;
            border: 1px solid #dfe5ec;
            border-radius: 12px;
            margin-top: 20px;
            padding: 22px;
        }

        .author-card-header {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .step-actions {
            align-items: center;
            border-top: 1px solid #edf0f4;
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 24px;
        }

        .btn-previous {
            background: #eef1f5;
            color: #26364a;
        }

        .btn-remove {
            background: transparent;
            border: 1px solid #dc3545;
            border-radius: 7px;
            color: #dc3545;
            padding: 7px 12px;
        }

        .empty-journal-notice {
            background: #fff6df;
            border: 1px solid #f0d589;
            border-radius: 8px;
            color: #725b20;
            padding: 13px 15px;
        }

        @media (max-width: 767px) {

            .submission-header,
            .submission-introduction,
            .submission-step {
                padding: 24px 20px;
            }

            .submission-progress {
                gap: 6px;
                padding: 22px 16px 6px;
            }

            .progress-item {
                font-size: 0;
                text-align: center;
            }

            .progress-item span {
                font-size: 13px;
                margin-right: 0;
            }
        }
    </style>
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <div class="ltn__contact-message-area mb-120">
        <div class="container">
            <div class="submission-shell">
                <div class="submission-card white-bg">
                    <div class="submission-header">
                        {{-- <span class="submission-header-label">Online Submission</span> --}}
                        <h2>Submit Your Manuscript</h2>
                        <p class="mb-0">Follow the steps below to complete your submission.</p>
                    </div>

                    <div class="submission-progress" aria-label="Submission progress">
                        <div class="progress-item active" data-progress="0"><span>1</span> Introduction</div>
                        <div class="progress-item" data-progress="1"><span>2</span> Account</div>
                        <div class="progress-item" data-progress="2"><span>3</span> Article</div>
                        <div class="progress-item" data-progress="3"><span>4</span> International</div>
                        <div class="progress-item" data-progress="4"><span>5</span> Declarations</div>
                    </div>

                    <form id="manuscript-submission-form" action="{{ route('manuscript-submission.store') }}"
                        method="POST">
                        @csrf

                        <section class="submission-step submission-introduction active" data-step="0">
                            <h3>Manuscript Submission Form</h3>

                            <p>
                                This form is provided by <strong><em>Rumah Jurnal UIN Sjech M. Djamil Djambek
                                        Bukittinggi</em></strong> as a facility for authors to submit manuscripts for
                                preliminary editorial review.
                            </p>

                            <p>
                                Authors are required to provide complete and accurate information and article metadata.
                                Manuscripts that comply with the journal’s aims, scope, and submission requirements may
                                proceed to the formal submission stage through the journal’s online system.
                            </p>

                            <p>
                                Upon editorial approval, authors will receive a username and password created by the
                                editor. Authors must then submit the manuscript to the intended journal through
                                <a href="https://ejournal.uinbukittinggi.ac.id/" target="_blank"
                                    rel="noopener noreferrer">https://ejournal.uinbukittinggi.ac.id/</a>.
                            </p>

                            <p>
                                Submission through this form does not guarantee acceptance for publication. All
                                manuscripts remain subject to the editorial screening, peer-review, revision, and
                                publication policies of the respective journal.
                            </p>

                            <div class="step-actions">
                                <span></span>
                                <button class="btn theme-btn-1 btn-effect-1 next-step" type="button">
                                    Start Submission
                                </button>
                            </div>
                        </section>

                        <section class="submission-step" data-step="1">
                            <div class="step-title">
                                <h3 class="mb-0">A. Corresponding Author Account Information</h3>
                                <p class="mb-0">This information will be used to identify and contact the corresponding
                                    author.</p>
                            </div>

                            <div class="row">
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="first_name">First Name <span
                                            class="required-mark">*</span></label>
                                    <input class="form-control" id="first_name" name="first_name" type="text"
                                        value="{{ old('first_name') }}" maxlength="100" autocomplete="given-name" required>
                                    @error('first_name')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="last_name">Last Name <span
                                            class="required-mark">*</span></label>
                                    <input class="form-control" id="last_name" name="last_name" type="text"
                                        value="{{ old('last_name') }}" maxlength="100" autocomplete="family-name" required>
                                    @error('last_name')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="email">Active Email Address <span
                                            class="required-mark">*</span></label>
                                    <input class="form-control" id="email" name="email" type="email"
                                        value="{{ old('email') }}" maxlength="255" autocomplete="email" required>
                                    @error('email')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="username">Username <span
                                            class="required-mark">*</span></label>
                                    <input class="form-control" id="username" name="username" type="text"
                                        value="{{ old('username') }}" minlength="4" maxlength="50"
                                        autocomplete="username" pattern="[A-Za-z0-9_-]+" required>
                                    <span class="field-help">Letters, numbers, hyphens, and underscores only.</span>
                                    @error('username')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="password">Password <span
                                            class="required-mark">*</span></label>
                                    <input class="form-control" id="password" name="password" type="password"
                                        minlength="8" autocomplete="new-password" required>
                                    <span class="field-help">Use at least 8 characters.</span>
                                    @error('password')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="password_confirmation">Confirm Password <span
                                            class="required-mark">*</span></label>
                                    <input class="form-control" id="password_confirmation" name="password_confirmation"
                                        type="password" minlength="8" autocomplete="new-password" required>
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="whatsapp_number">WhatsApp Number <span
                                            class="required-mark">*</span></label>
                                    <input class="form-control" id="whatsapp_number" name="whatsapp_number"
                                        type="tel" value="{{ old('whatsapp_number') }}" maxlength="30"
                                        autocomplete="tel" placeholder="+62 812 3456 7890" required>
                                    @error('whatsapp_number')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="institution">Institution/Affiliation <span
                                            class="required-mark">*</span></label>
                                    <input class="form-control" id="institution" name="institution" type="text"
                                        value="{{ old('institution') }}" maxlength="255" autocomplete="organization"
                                        required>
                                    @error('institution')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="country">Country <span
                                            class="required-mark">*</span></label>
                                    <input class="form-control" id="country" name="country" type="text"
                                        value="{{ old('country') }}" maxlength="100" autocomplete="country-name"
                                        required>
                                    @error('country')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="orcid_id">ORCID iD</label>
                                    <input class="form-control" id="orcid_id" name="orcid_id" type="text"
                                        value="{{ old('orcid_id') }}" maxlength="50" placeholder="0000-0002-1825-0097">
                                    @error('orcid_id')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 field-wrap">
                                    <label class="form-label" for="scopus_or_scholar_url">Scopus Author ID or Google
                                        Scholar Profile URL</label>
                                    <input class="form-control" id="scopus_or_scholar_url" name="scopus_or_scholar_url"
                                        type="text" value="{{ old('scopus_or_scholar_url') }}" maxlength="500">
                                    @error('scopus_or_scholar_url')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="step-actions">
                                <button class="btn btn-previous previous-step" type="button">Previous</button>
                                <button class="btn theme-btn-1 btn-effect-1 next-step" type="button">Next: Article
                                    Information</button>
                            </div>
                        </section>

                        <section class="submission-step" data-step="2">
                            <div class="step-title">
                                <h3 class="mb-0">B. Article Information</h3>
                                <p class="mb-0">Provide the article metadata and choose the intended journal.</p>
                            </div>

                            <div class="row">
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="target_journal_id">Target Journal <span
                                            class="required-mark">*</span></label>
                                    @if ($journals->isEmpty())
                                        <div class="empty-journal-notice">No active UIN journal is currently available.
                                        </div>
                                    @else
                                        <select class="form-select" id="target_journal_id" name="target_journal_id"
                                            required>
                                            <option value="">Select the intended UIN journal</option>
                                            @foreach ($journals as $journal)
                                                <option value="{{ $journal->id }}" @selected(old('target_journal_id') == $journal->id)>
                                                    {{ $journal->name ?: $journal->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                    @error('target_journal_id')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="article_type">Article Type <span
                                            class="required-mark">*</span></label>
                                    <select class="form-select" id="article_type" name="article_type" required>
                                        <option value="">Select article type</option>
                                        <option value="research_article" @selected(old('article_type') === 'research_article')>Research Article
                                        </option>
                                        <option value="community_service_article" @selected(old('article_type') === 'community_service_article')>Community
                                            Service Article</option>
                                        <option value="systematic_literature_review" @selected(old('article_type') === 'systematic_literature_review')>
                                            Systematic Literature Review</option>
                                    </select>
                                    @error('article_type')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="article_language">Language of the Article <span
                                            class="required-mark">*</span></label>
                                    <select class="form-select" id="article_language" name="article_language" required>
                                        <option value="">Select language</option>
                                        <option value="Indonesian" @selected(old('article_language') === 'Indonesian')>Indonesian</option>
                                        <option value="English" @selected(old('article_language') === 'English')>English</option>
                                        <option value="Arabic" @selected(old('article_language') === 'Arabic')>Arabic</option>
                                        <option value="Other" @selected(old('article_language') === 'Other')>Other</option>
                                    </select>
                                    @error('article_language')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 field-wrap">
                                    <label class="form-label" for="article_title">Article Title <span
                                            class="required-mark">*</span></label>
                                    <input class="form-control" id="article_title" name="article_title" type="text"
                                        value="{{ old('article_title') }}" maxlength="500" required>
                                    @error('article_title')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 field-wrap">
                                    <label class="form-label" for="abstract">Abstract <span
                                            class="required-mark">*</span></label>
                                    <textarea class="form-control" id="abstract" name="abstract" minlength="100" rows="7" required>{{ old('abstract') }}</textarea>
                                    <span class="field-help">Minimum 100 characters.</span>
                                    @error('abstract')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 field-wrap">
                                    <label class="form-label">Keywords <span class="required-mark">*</span></label>
                                    <span class="field-help mb-10">Enter 3–5 unique words or phrases.</span>
                                    <div id="keyword-list">
                                        @php
                                            $oldKeywords = old('keywords', ['', '', '']);
                                            $oldKeywords =
                                                count($oldKeywords) >= 3
                                                    ? $oldKeywords
                                                    : array_pad($oldKeywords, 3, '');
                                        @endphp
                                        @foreach ($oldKeywords as $keyword)
                                            <div class="keyword-row">
                                                <input class="form-control keyword-input" name="keywords[]"
                                                    type="text" value="{{ $keyword }}" maxlength="100" required>
                                                @if ($loop->index >= 3)
                                                    <button class="btn-remove remove-keyword"
                                                        type="button">Remove</button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-previous" id="add-keyword" type="button">+ Add
                                        keyword</button>
                                    @error('keywords')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                    @error('keywords.*')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 field-wrap">
                                    <label class="form-label" for="reference_list">Reference List <span
                                            class="required-mark">*</span></label>
                                    <textarea class="form-control" id="reference_list" name="reference_list" rows="9"
                                        placeholder="Enter one reference per line." required>{{ old('reference_list') }}</textarea>
                                    @error('reference_list')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="correspondence_email">Correspondence Email</label>
                                    <input class="form-control" id="correspondence_email" name="correspondence_email"
                                        type="email" value="{{ old('correspondence_email') }}" maxlength="255">
                                    <span class="field-help">Complete only if different from the account email.</span>
                                    @error('correspondence_email')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 field-wrap">
                                    <label class="form-label" for="funding_source">Funding Source</label>
                                    <input class="form-control" id="funding_source" name="funding_source" type="text"
                                        value="{{ old('funding_source') }}" maxlength="255">
                                    <span class="field-help">If applicable.</span>
                                    @error('funding_source')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="step-actions">
                                <button class="btn btn-previous previous-step" type="button">Previous</button>
                                <button class="btn theme-btn-1 btn-effect-1 next-step" type="button">Next: International
                                    Authors</button>
                            </div>
                        </section>

                        <section class="submission-step" data-step="3">
                            <div class="step-title">
                                <h3 class="mb-0">C. Additional Information for International Authors</h3>
                                <p class="mb-0">Optional—complete this section if the article includes one or more
                                    international authors.</p>
                            </div>

                            <input name="has_international_authors" type="hidden" value="0">
                            <label class="international-toggle">
                                <input id="has_international_authors" name="has_international_authors" type="checkbox"
                                    value="1" @checked(old('has_international_authors'))>
                                <span>This article includes one or more international authors.</span>
                            </label>

                            <div id="international-authors-section" hidden>
                                <div id="international-authors-list">
                                    @php
                                        $oldInternationalAuthors = old('international_authors', [[]]);
                                    @endphp
                                    @foreach ($oldInternationalAuthors as $index => $author)
                                        <div class="international-author-card" data-author-card>
                                            <div class="author-card-header">
                                                <h4 class="author-card-title mb-0">International Author
                                                    {{ $index + 1 }}</h4>
                                                <button class="btn-remove remove-author" type="button">Remove</button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 field-wrap">
                                                    <label class="form-label">Full Name of Institution in English <span
                                                            class="required-mark">*</span></label>
                                                    <input class="form-control international-required"
                                                        name="international_authors[{{ $index }}][institution_name]"
                                                        type="text" value="{{ $author['institution_name'] ?? '' }}"
                                                        maxlength="255">
                                                </div>
                                                <div class="col-md-6 field-wrap">
                                                    <label class="form-label">Department/Faculty <span
                                                            class="required-mark">*</span></label>
                                                    <input class="form-control international-required"
                                                        name="international_authors[{{ $index }}][department]"
                                                        type="text" value="{{ $author['department'] ?? '' }}"
                                                        maxlength="255">
                                                </div>
                                                <div class="col-md-6 field-wrap">
                                                    <label class="form-label">Country <span
                                                            class="required-mark">*</span></label>
                                                    <input class="form-control international-required"
                                                        name="international_authors[{{ $index }}][country]"
                                                        type="text" value="{{ $author['country'] ?? '' }}"
                                                        maxlength="100">
                                                </div>
                                                <div class="col-md-6 field-wrap">
                                                    <label class="form-label">Institutional Email Address <span
                                                            class="required-mark">*</span></label>
                                                    <input class="form-control international-required"
                                                        name="international_authors[{{ $index }}][institutional_email]"
                                                        type="email" value="{{ $author['institutional_email'] ?? '' }}"
                                                        maxlength="255">
                                                </div>
                                                <div class="col-md-6 field-wrap">
                                                    <label class="form-label">ORCID iD or Scopus Author ID <span
                                                            class="required-mark">*</span></label>
                                                    <input class="form-control international-required"
                                                        name="international_authors[{{ $index }}][orcid_or_scopus_id]"
                                                        type="text" value="{{ $author['orcid_or_scopus_id'] ?? '' }}"
                                                        maxlength="255">
                                                </div>
                                                <div class="col-md-12 field-wrap">
                                                    <label class="form-label">Author’s Contribution to the Article <span
                                                            class="required-mark">*</span></label>
                                                    <textarea class="form-control international-required" name="international_authors[{{ $index }}][contribution]"
                                                        rows="4" maxlength="1000">{{ $author['contribution'] ?? '' }}</textarea>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="consent-item">
                                                        <input class="international-required"
                                                            name="international_authors[{{ $index }}][consent]"
                                                            type="checkbox" value="1" @checked($author['consent'] ?? false)>
                                                        <span>Confirmation of consent to be listed as an author.</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button class="btn btn-previous mt-20" id="add-international-author" type="button">
                                    + Add another international author
                                </button>

                                <label class="consent-item mt-25">
                                    <input id="international_author_confirmation" name="international_author_confirmation"
                                        type="checkbox" value="1" @checked(old('international_author_confirmation'))>
                                    <strong>I confirm that the international author has agreed to be listed as an author of
                                        this article.</strong>
                                </label>
                            </div>

                            @error('international_authors')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                            @error('international_authors.*.*')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                            @error('international_author_confirmation')
                                <span class="field-error">{{ $message }}</span>
                            @enderror

                            <div class="step-actions">
                                <button class="btn btn-previous previous-step" type="button">Previous</button>
                                <button class="btn theme-btn-1 btn-effect-1 next-step" type="button">Next:
                                    Declarations</button>
                            </div>
                        </section>

                        <section class="submission-step" data-step="4">
                            <div class="step-title">
                                <h3 class="mb-0">D. Author Declarations</h3>
                                <p class="mb-0">Please confirm every declaration before submitting the form.</p>
                            </div>

                            @php
                                $declarations = [
                                    'is_original_work' => 'I confirm that this article is an original work.',
                                    'not_previously_published' =>
                                        'I confirm that this article has not been previously published.',
                                    'not_under_consideration' =>
                                        'I confirm that this article is not currently under consideration by another journal.',
                                    'all_authors_approved' =>
                                        'I confirm that all authors have approved the submission of this article.',
                                    'authorship_information_correct' =>
                                        'I confirm that the authors’ information and authorship order are correct.',
                                    'international_authors_agreed' =>
                                        'I confirm that all international authors, if applicable, have agreed to have their names and affiliations included in the article.',
                                    'uses_official_template' =>
                                        'I confirm that the manuscript has been prepared using the journal’s official template.',
                                    'agrees_peer_review' =>
                                        'I agree to participate in the peer-review and revision processes.',
                                    'agrees_publication_process' =>
                                        'I agree to comply with all stages and requirements of the publication process.',
                                    'agrees_publication_fees' =>
                                        'I agree to pay the applicable publication fees in accordance with the publisher’s policies and regulations.',
                                ];
                            @endphp

                            @foreach ($declarations as $field => $label)
                                <label class="declaration-item">
                                    <input name="{{ $field }}" type="checkbox" value="1"
                                        @checked(old($field)) required>
                                    <span>{{ $label }} <span class="required-mark">*</span></span>
                                </label>
                                @error($field)
                                    <span class="field-error mb-10">{{ $message }}</span>
                                @enderror
                            @endforeach

                            <div class="step-actions">
                                <button class="btn btn-previous previous-step" type="button">Previous</button>
                                <button class="btn theme-btn-1 btn-effect-1" type="submit">
                                    Submit Manuscript Information
                                </button>
                            </div>
                        </section>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <template id="international-author-template">
        <div class="international-author-card" data-author-card>
            <div class="author-card-header">
                <h4 class="author-card-title mb-0">International Author</h4>
                <button class="btn-remove remove-author" type="button">Remove</button>
            </div>
            <div class="row">
                <div class="col-md-12 field-wrap">
                    <label class="form-label">Full Name of Institution in English <span
                            class="required-mark">*</span></label>
                    <input class="form-control international-required" data-field="institution_name" type="text"
                        maxlength="255">
                </div>
                <div class="col-md-6 field-wrap">
                    <label class="form-label">Department/Faculty <span class="required-mark">*</span></label>
                    <input class="form-control international-required" data-field="department" type="text"
                        maxlength="255">
                </div>
                <div class="col-md-6 field-wrap">
                    <label class="form-label">Country <span class="required-mark">*</span></label>
                    <input class="form-control international-required" data-field="country" type="text"
                        maxlength="100">
                </div>
                <div class="col-md-6 field-wrap">
                    <label class="form-label">Institutional Email Address <span class="required-mark">*</span></label>
                    <input class="form-control international-required" data-field="institutional_email" type="email"
                        maxlength="255">
                </div>
                <div class="col-md-6 field-wrap">
                    <label class="form-label">ORCID iD or Scopus Author ID <span class="required-mark">*</span></label>
                    <input class="form-control international-required" data-field="orcid_or_scopus_id" type="text"
                        maxlength="255">
                </div>
                <div class="col-md-12 field-wrap">
                    <label class="form-label">Author’s Contribution to the Article <span
                            class="required-mark">*</span></label>
                    <textarea class="form-control international-required" data-field="contribution" rows="4" maxlength="1000"></textarea>
                </div>
                <div class="col-md-12">
                    <label class="consent-item">
                        <input class="international-required" data-field="consent" type="checkbox" value="1">
                        <span>Confirmation of consent to be listed as an author.</span>
                    </label>
                </div>
            </div>
        </div>
    </template>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const steps = Array.from(document.querySelectorAll('.submission-step'));
            const progressItems = Array.from(document.querySelectorAll('.progress-item'));
            const form = document.getElementById('manuscript-submission-form');
            const internationalToggle = document.getElementById('has_international_authors');
            const internationalSection = document.getElementById('international-authors-section');
            const internationalList = document.getElementById('international-authors-list');
            const internationalConfirmation = document.getElementById('international_author_confirmation');
            const serverErrors = @json(array_keys($errors->toArray()));
            let currentStep = 0;

            const errorStepMap = [
                [],
                ['first_name', 'last_name', 'email', 'username', 'password', 'whatsapp_number', 'institution',
                    'country', 'orcid_id', 'scopus_or_scholar_url'
                ],
                ['target_journal_id', 'article_type', 'article_language', 'article_title', 'abstract',
                    'keywords', 'reference_list', 'correspondence_email', 'funding_source'
                ],
                ['has_international_authors', 'international_authors', 'international_author_confirmation'],
                ['is_original_work', 'not_previously_published', 'not_under_consideration',
                    'all_authors_approved', 'authorship_information_correct', 'international_authors_agreed',
                    'uses_official_template', 'agrees_peer_review', 'agrees_publication_process',
                    'agrees_publication_fees'
                ],
            ];

            if (serverErrors.length) {
                const detectedStep = errorStepMap.findIndex(function(fields) {
                    return serverErrors.some(function(error) {
                        return fields.some(function(field) {
                            return error === field || error.startsWith(field + '.');
                        });
                    });
                });
                currentStep = detectedStep >= 0 ? detectedStep : 0;
            }

            function showStep(index) {
                currentStep = Math.max(0, Math.min(index, steps.length - 1));
                steps.forEach((step, stepIndex) => step.classList.toggle('active', stepIndex === currentStep));
                progressItems.forEach(function(item, itemIndex) {
                    item.classList.toggle('active', itemIndex === currentStep);
                    item.classList.toggle('completed', itemIndex < currentStep);
                });
                document.querySelector('.submission-card').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            function validateCurrentStep() {
                const fields = Array.from(steps[currentStep].querySelectorAll('input, select, textarea'))
                    .filter(field => !field.disabled && field.type !== 'hidden');

                for (const field of fields) {
                    if (!field.checkValidity()) {
                        field.reportValidity();
                        field.focus();
                        return false;
                    }
                }
                return true;
            }

            document.querySelectorAll('.next-step').forEach(function(button) {
                button.addEventListener('click', function() {
                    if (validateCurrentStep()) {
                        showStep(currentStep + 1);
                    }
                });
            });

            document.querySelectorAll('.previous-step').forEach(function(button) {
                button.addEventListener('click', function() {
                    showStep(currentStep - 1);
                });
            });

            function refreshKeywords() {
                const rows = Array.from(document.querySelectorAll('.keyword-row'));
                document.getElementById('add-keyword').disabled = rows.length >= 5;
                rows.forEach(function(row, index) {
                    const removeButton = row.querySelector('.remove-keyword');
                    if (removeButton) {
                        removeButton.hidden = index < 3;
                    }
                });
            }

            document.getElementById('add-keyword').addEventListener('click', function() {
                const list = document.getElementById('keyword-list');
                if (list.children.length >= 5) return;

                const row = document.createElement('div');
                row.className = 'keyword-row';
                row.innerHTML =
                    '<input class="form-control keyword-input" name="keywords[]" type="text" maxlength="100" required>' +
                    '<button class="btn-remove remove-keyword" type="button">Remove</button>';
                list.appendChild(row);
                refreshKeywords();
                row.querySelector('input').focus();
            });

            document.getElementById('keyword-list').addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-keyword')) {
                    event.target.closest('.keyword-row').remove();
                    refreshKeywords();
                }
            });

            function reindexInternationalAuthors() {
                Array.from(internationalList.querySelectorAll('[data-author-card]')).forEach(function(card, index) {
                    card.querySelector('.author-card-title').textContent = 'International Author ' + (
                        index + 1);
                    card.querySelectorAll('[data-field]').forEach(function(field) {
                        field.name = 'international_authors[' + index + '][' + field.dataset.field +
                            ']';
                    });
                    card.querySelectorAll('[name^="international_authors["]').forEach(function(field) {
                        field.name = field.name.replace(/international_authors\[\d+\]/,
                            'international_authors[' + index + ']');
                    });
                });
            }

            function syncInternationalSection() {
                const enabled = internationalToggle.checked;
                internationalSection.hidden = !enabled;
                internationalSection.querySelectorAll('.international-required').forEach(field => field.required =
                    enabled);
                internationalConfirmation.required = enabled;
            }

            internationalToggle.addEventListener('change', syncInternationalSection);

            document.getElementById('add-international-author').addEventListener('click', function() {
                const fragment = document.getElementById('international-author-template').content.cloneNode(
                    true);
                internationalList.appendChild(fragment);
                reindexInternationalAuthors();
                syncInternationalSection();
            });

            internationalList.addEventListener('click', function(event) {
                if (!event.target.classList.contains('remove-author')) return;

                const cards = internationalList.querySelectorAll('[data-author-card]');
                if (cards.length === 1) {
                    cards[0].querySelectorAll('input, textarea').forEach(function(field) {
                        if (field.type === 'checkbox') {
                            field.checked = false;
                        } else {
                            field.value = '';
                        }
                    });
                    return;
                }

                event.target.closest('[data-author-card]').remove();
                reindexInternationalAuthors();
            });

            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    const invalidField = form.querySelector(':invalid');
                    const invalidStep = steps.findIndex(step => step.contains(invalidField));
                    showStep(invalidStep >= 0 ? invalidStep : currentStep);
                    setTimeout(() => invalidField.reportValidity(), 50);
                }
            });

            reindexInternationalAuthors();
            syncInternationalSection();
            refreshKeywords();
            showStep(currentStep);
        });
    </script>
@endsection
