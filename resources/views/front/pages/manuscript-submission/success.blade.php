@extends('front.app')

@section('seo')
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="robots" content="noindex, nofollow">
    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <div class="ltn__contact-message-area mb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="ltn__form-box contact-form-box box-shadow white-bg text-center"
                        style="border-radius: 16px; padding: 55px 40px;">
                        <div class="mb-25" style="font-size: 70px; color: #28a745;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2>Submission Received</h2>
                        <p>
                            Thank you. Your manuscript information has been saved and is waiting for administrative review.
                        </p>

                        <div class="mt-30 mb-30"
                            style="background: #f6f8fb; border: 1px solid #e4e9ef; border-radius: 10px; padding: 20px;">
                            <p class="mb-5"><strong>Submission reference</strong></p>
                            <code style="font-size: 16px; overflow-wrap: anywhere;">{{ $submission->submission_code }}</code>
                            <p class="mt-15 mb-0">{{ $submission->article_title }}</p>
                        </div>

                        <p class="text-muted">
                            Keep this reference for future communication. Do not share it publicly.
                        </p>

                        <div class="btn-wrapper mt-30">
                            <a class="btn theme-btn-1 btn-effect-1" href="{{ route('home') }}">Back to Home</a>
                            <a class="btn btn-transparent btn-effect-3" href="{{ route('manuscript-submission.create') }}">
                                Submit Another Manuscript
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
