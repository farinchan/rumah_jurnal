@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('contact.index') }}">
    <link rel="canonical" href="{{ route('contact.index') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <!-- CONTACT ADDRESS AREA START -->
    <div class="ltn__contact-address-area mb-90">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3 box-shadow">
                        <div class="ltn__contact-address-icon">
                            <img src="{{ asset("front/img/icons/10.png") }}" alt="Icon Image">
                        </div>
                        <h3>{{ __('front.email_address') }}</h3>
                        <p>
                            {{ $setting_web->email }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3 box-shadow">
                        <div class="ltn__contact-address-icon">
                            <img src="{{ asset("front/img/icons/11.png") }}" alt="Icon Image">
                        </div>
                        <h3>{{ __('front.phone_number') }}</h3>
                        <p>
                            {{ $setting_web->phone }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3 box-shadow">
                        <div class="ltn__contact-address-icon">
                            <img src="{{ asset("front/img/icons/12.png") }}" alt="Icon Image">
                        </div>
                        <h3>{{ __('front.office_address') }}</h3>
                        <p>
                            {{ $setting_web->address }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CONTACT ADDRESS AREA END -->

    <!-- CONTACT MESSAGE AREA START -->
    <div class="ltn__contact-message-area mb-120 mb--100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__form-box contact-form-box box-shadow white-bg">
                        <h4 class="title-2">>{{ __('front.contact_us') }}</h4>
                        <form id="contact-form" action="{{ route('contact.send') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-item input-item-name ltn__custom-icon">
                                        <input type="text" name="name" placeholder="{{ __('front.enter_your_name') }}" value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-item input-item-email ltn__custom-icon">
                                        <input type="email" name="email" placeholder="{{ __('front.enter_your_email') }}" value="{{ old('email') }}" required>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-item input-item-phone ltn__custom-icon">
                                        <input type="text" name="phone" placeholder="{{ __('front.enter_your_phone') }}" value="{{ old('phone') }}" required>
                                        @error('phone')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-item  ltn__custom-icon">
                                        <input type="text" name="subject" placeholder="{{ __('front.enter_subject') }}" value="{{ old('subject') }}" required>
                                        @error('subject')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="input-item input-item-textarea ltn__custom-icon">
                                <textarea name="message" placeholder="{{ __('front.enter_message') }}" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <p><label class="input-info-save mb-0"><input type="checkbox" name="agree">
                                {{ __('front.message_checkbox') }}
                            </label></p>
                            <div>
                                {!! NoCaptcha::renderJs() !!}
                                {!! NoCaptcha::display() !!}

                                @error('g-recaptcha-response')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="btn-wrapper ">
                                <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit">
                                    {{ __('front.send_message') }}
                                </button>
                            </div>
                            <p class="form-messege mb-0 mt-20"></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CONTACT MESSAGE AREA END -->

    <!-- GOOGLE MAP AREA START -->
    <div class="google-map mb-120">

        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d320.84241104217386!2d100.39786475676972!3d-0.3219989136461684!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2fd539e261f53f89%3A0xedfc275ef4afd8d0!2sRumah%20Jurnal%20UIN%20Bukittinggi!5e0!3m2!1sid!2sid!4v1741270898403!5m2!1sid!2sid" width="100%" height="100%" frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>

    </div>

@endsection
