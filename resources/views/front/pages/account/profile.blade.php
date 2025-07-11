@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">
    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('announcement.index') }}">
    <link rel="canonical" href="{{ route('announcement.index') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection
@section('styles')
@endsection
@section('content')
    @include('front.partials.breadcrumb')
    <!-- WISHLIST AREA START -->
    <div class="liton__wishlist-area pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- PRODUCT TAB AREA START -->
                    <div class="ltn__product-tab-area">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="widget ltn__author-widget box-shadow">
                                        <div class="ltn__author-widget-inner text-center">
                                            <img src="{{ $me->getPhoto() }}" alt="Image">
                                            <h5 style="margin-bottom: 3px;">{{ $me->name }}</h5>
                                            <p style="margin-bottom: 0;">{{ $me->email }}</p>
                                        </div>
                                    </div>
                                    <div class="ltn__tab-menu-list mb-50">
                                        <div class="nav" style="margin-right: 0;">
                                            <a class="active show" data-toggle="tab" href="#liton_tab_1_1">Dashboard <i
                                                    class="fas fa-home"></i></a>
                                            <a data-toggle="tab" href="#liton_tab_1_2">Event Registered <i
                                                    class="fas fa-file-alt"></i></a>
                                            <a data-toggle="tab" href="#liton_tab_1_5">Account Details <i
                                                    class="fas fa-user"></i></a>
                                            <a href="{{ route("logout") }}">Logout <i class="fas fa-sign-out-alt"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="liton_tab_1_1">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>Hello <strong>{{ $me->name }}</strong></p>

                                                <p>
                                                    Welcome to your account dashboard. Here you can view your recent
                                                    activities, manage your events, and update your account information.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="liton_tab_1_2">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                @forelse ($events as $event)
                                                    @php
                                                        [$before, $after] = explode(' - ', $event->event->datetime);
                                                        $date_before = \Carbon\Carbon::parse(
                                                            $before,
                                                        )->toDateTimeString();
                                                        $date_after = \Carbon\Carbon::parse($after)->toDateTimeString();
                                                        // dd($date_before, $date_after);
                                                    @endphp
                                                    <div class="widget ltn__author-widget">
                                                        <div class="ltn__author-widget-inner ">
                                                            <a href="{{ route('event.show', $event->event->slug) }}">

                                                                <h3>{{ $event->event->name }}</h3>


                                                            </a>
                                                            <div class="ltn__blog-meta">
                                                                <ul
                                                                    style="display: flex; flex-direction: column; gap: 8px;">
                                                                    <li class="ltn__blog-author">
                                                                        <a href="#"><i
                                                                                class="fa fa-info-circle"></i>{{ $event->event->type }}</a>
                                                                    </li>
                                                                    <li class="ltn__blog-author">
                                                                        <a href="#"><i class="fa fa-bullhorn"></i>
                                                                            {{ $event->event->status }}</a>
                                                                    </li>
                                                                    <li class="ltn__blog-author">
                                                                        <a href="#"><i class="far fa-calendar"></i>
                                                                            {{ $event->event->datetime }}
                                                                        </a>
                                                                    </li>
                                                                    @if ($event->event->status == 'offline')
                                                                        <li class="ltn__blog-author">
                                                                            <a href="#"><i
                                                                                    class="fa fa-map-marker"></i>
                                                                                {{ $event->event->location }}
                                                                            </a>
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            </div>

                                                            <p class="text-success">
                                                                <b>
                                                                    <i class="fa fa-check-circle"></i>
                                                                    {{ __('front.registered') }}
                                                                </b>
                                                            </p>
                                                            <div
                                                                class=" ltn__menu-widget ltn__menu-widget-2 text-uppercase">
                                                                <ul>
                                                                    <li>
                                                                        <a href="{{ route('event.eticket', $event->id) }}"
                                                                            target="_blank">
                                                                            {{ __('front.print_eticket') }}

                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#" target="_blank">
                                                                            Download Certificate

                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="liton_tab_1_5">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>
                                                    <strong>Edit your account information</strong> <br>
                                                    You can change your password, email, and other personal information
                                                    here.
                                                </p>
                                                <div class="ltn__form-box">
                                                    <form action="#">
                                                        <fieldset class="mb-30">
                                                            <legend>Biodata</legend>
                                                            <div class="row x">
                                                                <div class="col-md-12">
                                                                    <label>Name</label>
                                                                    <input type="text" name="name" placeholder="Name"
                                                                        value="{{ $me->name }}">
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label>Gender</label>
                                                                    <div class="input-item">
                                                                        <select name="gender" class="nice-select">
                                                                            <option value="laki-laki"
                                                                                {{ $me->gender == 'laki-laki' ? 'selected' : '' }}>
                                                                                Laki-laki</option>
                                                                            <option value="perempuan"
                                                                                {{ $me->gender == 'perempuan' ? 'selected' : '' }}>
                                                                                Perempuan</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label>Email</label>
                                                                    <input type="email" name="email"
                                                                        placeholder="Email" value="{{ $me->email }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label>Phone</label>
                                                                    <input type="text" name="phone"
                                                                        placeholder="Phone" value="{{ $me->phone }}">
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <fieldset>
                                                            <legend>Password change</legend>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label>Current password (leave blank to leave
                                                                        unchanged):</label>
                                                                    <input type="password" name="ltn__name">
                                                                    <label>New password (leave blank to leave
                                                                        unchanged):</label>
                                                                    <input type="password" name="ltn__lastname">
                                                                    <label>Confirm new password:</label>
                                                                    <input type="password" name="ltn__lastname">
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <div class="btn-wrapper">
                                                            <button type="submit"
                                                                class="btn theme-btn-1 btn-effect-1 text-uppercase">Save
                                                                Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- PRODUCT TAB AREA END -->
                </div>
            </div>
        </div>
    </div>
    <!-- WISHLIST AREA START -->
@endsection
