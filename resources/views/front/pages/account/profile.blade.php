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
                                            <a class="active show" data-toggle="tab"
                                                href="#liton_tab_1_1">{{ __('front.overview') }} <i
                                                    class="fas fa-home"></i></a>
                                            <a data-toggle="tab" href="#liton_tab_1_2">{{ __('front.event_registered') }}
                                                <i class="fas fa-file-alt"></i></a>
                                            <a data-toggle="tab" href="#liton_tab_1_5">{{ __('front.account_details') }} <i
                                                    class="fas fa-user"></i></a>
                                            <a href="{{ route('logout') }}">{{ __('front.logout') }} <i
                                                    class="fas fa-sign-out-alt"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="liton_tab_1_1">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>{{ __('front.hello') }} <strong>{{ $me->name }}</strong></p>

                                                <p>
                                                    {{ __('front.welcome_dashboard') }}
                                                </p>

                                                <!-- Event Statistics -->
                                                <div class="row mb-4">
                                                    @php
                                                        $totalEvents = $events->count();
                                                        $completedEvents = 0;
                                                        $upcomingEvents = 0;
                                                        $ongoingEvents = 0;

                                                        foreach ($events as $event) {
                                                            try {
                                                                [$before, $after] = explode(
                                                                    ' - ',
                                                                    $event->event->datetime,
                                                                );
                                                                $startDate = \Carbon\Carbon::parse($before);
                                                                $endDate = \Carbon\Carbon::parse($after);
                                                                $now = \Carbon\Carbon::now();

                                                                if ($now->gt($endDate)) {
                                                                    $completedEvents++;
                                                                } elseif ($now->between($startDate, $endDate)) {
                                                                    $ongoingEvents++;
                                                                } else {
                                                                    $upcomingEvents++;
                                                                }
                                                            } catch (\Exception $e) {
                                                                // Handle parsing errors gracefully
                                                                continue;
                                                            }
                                                        }
                                                    @endphp

                                                    <div class="col-md-3">
                                                        <div class="card text-center border-primary">
                                                            <div class="card-body">
                                                                <h3 class="text-primary mb-1">{{ $totalEvents }}</h3>
                                                                <small
                                                                    class="text-muted">{{ __('front.total_events') }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card text-center border-success">
                                                            <div class="card-body">
                                                                <h3 class="text-success mb-1">{{ $completedEvents }}</h3>
                                                                <small
                                                                    class="text-muted">{{ __('front.completed_events') }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card text-center border-warning">
                                                            <div class="card-body">
                                                                <h3 class="text-warning mb-1">{{ $ongoingEvents }}</h3>
                                                                <small
                                                                    class="text-muted">{{ __('front.ongoing_events') }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card text-center border-info">
                                                            <div class="card-body">
                                                                <h3 class="text-info mb-1">{{ $upcomingEvents }}</h3>
                                                                <small
                                                                    class="text-muted">{{ __('front.upcoming_events') }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Event Status Information -->
                                                @if ($totalEvents > 0)
                                                    <div class="alert alert-info mb-4">
                                                        <h5><i class="fas fa-info-circle"></i>
                                                            {{ __('front.event_status_information') }}</h5>
                                                        <ul class="mb-0" style="padding-left: 20px;">
                                                            @if ($completedEvents > 0)
                                                                <li><strong>{{ $completedEvents }}</strong>
                                                                    {{ __('front.events_completed') }}</li>
                                                            @endif
                                                            @if ($ongoingEvents > 0)
                                                                <li><strong>{{ $ongoingEvents }}</strong>
                                                                    {{ __('front.events_ongoing') }}</li>
                                                            @endif
                                                            @if ($upcomingEvents > 0)
                                                                <li><strong>{{ $upcomingEvents }}</strong>
                                                                    {{ __('front.events_upcoming') }}</li>
                                                            @endif
                                                            @if ($totalEvents == 0)
                                                                <li>Anda belum mendaftar pada event manapun. Silakan lihat
                                                                    event yang tersedia dan daftar sekarang!</li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                @endif



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

                                                        // Determine event status
                                                        $startDate = \Carbon\Carbon::parse($before);
                                                        $endDate = \Carbon\Carbon::parse($after);
                                                        $now = \Carbon\Carbon::now();

                                                        $eventStatus = '';
                                                        $statusClass = '';
                                                        $statusIcon = '';

                                                        if ($now->gt($endDate)) {
                                                            $eventStatus = __('front.event_completed');
                                                            $statusClass = 'success';
                                                            $statusIcon = 'fa-check-circle';
                                                        } elseif ($now->between($startDate, $endDate)) {
                                                            $eventStatus = __('front.event_ongoing');
                                                            $statusClass = 'warning';
                                                            $statusIcon = 'fa-clock';
                                                        } else {
                                                            $eventStatus = __('front.event_upcoming');
                                                            $statusClass = 'info';
                                                            $statusIcon = 'fa-calendar-alt';
                                                        }
                                                        // dd($date_before, $date_after);
                                                    @endphp
                                                    <div class="widget ltn__author-widget">
                                                        <div class="ltn__author-widget-inner ">
                                                            <div
                                                                class="d-flex justify-content-between align-items-start mb-3">
                                                                <a href="{{ route('event.show', $event->event->slug) }}">
                                                                    <h3>{{ $event->event->name }}</h3>
                                                                </a>
                                                                <span class="badge badge-{{ $statusClass }} ml-2">
                                                                    <i class="fas {{ $statusIcon }}"></i>
                                                                    {{ $eventStatus }}
                                                                </span>
                                                            </div>

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

                                                            @if ($eventStatus == __('front.event_completed'))
                                                                <div class="alert alert-success mb-3">
                                                                    <small><i class="fas fa-info-circle"></i>
                                                                        {{ __('front.event_completed_message') }}</small>
                                                                </div>
                                                            @elseif($eventStatus == __('front.event_ongoing'))
                                                                <div class="alert alert-warning mb-3">
                                                                    <small><i class="fas fa-exclamation-triangle"></i>
                                                                        {{ __('front.event_ongoing_message') }}</small>
                                                                </div>
                                                            @else
                                                                <div class="alert alert-info mb-3">
                                                                    <small><i class="fas fa-calendar-check"></i>
                                                                        {{ __('front.event_upcoming_message') }}</small>
                                                                </div>
                                                            @endif

                                                            <div
                                                                class=" ltn__menu-widget ltn__menu-widget-2 text-uppercase">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <a href="{{ route('event.eticket', $event->id) }}"
                                                                            target="_blank"
                                                                            class="btn btn-sm btn-outline-primary btn-block mb-2">
                                                                            <i class="fas fa-ticket-alt"></i>
                                                                            {{ __('front.print_eticket') }}
                                                                        </a>
                                                                    </div>
                                                                    @if ($eventStatus == __('front.event_completed'))
                                                                        @if ($event->event->type != 'Rapat')
                                                                            <div class="col-md-4">
                                                                                <a href="{{ route('event.certificate', $event->id) }}"
                                                                                    target="_blank"
                                                                                    class="btn btn-sm btn-outline-success btn-block mb-2">
                                                                                    <i class="fas fa-certificate"></i>
                                                                                    {{ __('front.certificate') }}
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                        @if ($event->event->material)
                                                                            <div class="col-md-4">
                                                                                <a href="{{ Storage::url($event->event->material) }}"
                                                                                    target="_blank"
                                                                                    class="btn btn-sm btn-outline-info btn-block mb-2">
                                                                                    <i class="fas fa-download"></i>
                                                                                    {{ __('front.material') }}
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        @if ($event->event->type != 'Rapat')
                                                                            <div class="col-md-4">
                                                                                <button
                                                                                    class="btn btn-sm btn-outline-secondary btn-block mb-2"
                                                                                    disabled>
                                                                                    <i class="fas fa-certificate"></i>
                                                                                    {{ __('front.certificate') }}
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                        @if ($event->event->material)
                                                                            <div class="col-md-4">
                                                                                <a href="{{ Storage::url($event->event->material) }}"
                                                                                    target="_blank"
                                                                                    class="btn btn-sm btn-outline-info btn-block mb-2">
                                                                                    <i class="fas fa-download"></i>
                                                                                    {{ __('front.material') }}
                                                                                </a>
                                                                            </div>
                                                                        @else
                                                                            <div class="col-md-4">
                                                                                <button
                                                                                    class="btn btn-sm btn-outline-secondary btn-block mb-2"
                                                                                    disabled>
                                                                                    <i class="fas fa-download"></i>
                                                                                    {{ __('front.material') }}
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-center py-5">
                                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                        <h5>{{ __('front.no_events_registered_title') }}</h5>
                                                        <p class="text-muted">{{ __('front.no_events_registered_desc') }}
                                                        </p>
                                                        <a href="{{ route('event.index') }}"
                                                            class="btn theme-btn-1 btn-effect-1">{{ __('front.browse_events') }}</a>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="liton_tab_1_5">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>
                                                    <strong>{{ __('front.edit_account_info') }}</strong> <br>
                                                    {{ __('front.edit_account_desc') }}
                                                </p>
                                                <div class="ltn__form-box">
                                                    <form action="{{ route('account.profile.update') }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <fieldset class="mb-30">
                                                            <legend>{{ __('front.biodata') }}</legend>
                                                            <div class="row x">
                                                                <div class="col-md-12">
                                                                    <label>Name</label>
                                                                    <input type="text" name="name"
                                                                        placeholder="Name"
                                                                        value="{{ old('name', $me->name) }}" required>
                                                                    @error('name')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label>{{ __('front.gender') }}</label>
                                                                    <div class="input-item">
                                                                        <select name="gender" class="nice-select"
                                                                            required>
                                                                            <option value="laki-laki"
                                                                                {{ old('gender', $me->gender) == 'laki-laki' ? 'selected' : '' }}>
                                                                                {{ __('front.male') }}</option>
                                                                            <option value="perempuan"
                                                                                {{ old('gender', $me->gender) == 'perempuan' ? 'selected' : '' }}>
                                                                                {{ __('front.female') }}</option>
                                                                        </select>
                                                                    </div>
                                                                    @error('gender')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label>Email</label>
                                                                    <input type="email" name="email"
                                                                        placeholder="Email"
                                                                        value="{{ old('email', $me->email) }}" required>
                                                                    @error('email')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label>Phone</label>
                                                                    <input type="text" name="phone"
                                                                        placeholder="Phone"
                                                                        value="{{ old('phone', $me->phone) }}">
                                                                    @error('phone')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label>{{ __('front.photo_profile') }}</label>
                                                                    <input type="file" name="photo"
                                                                        accept="image/*">
                                                                    @error('photo')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                    @if ($me->photo)
                                                                        <small
                                                                            class="text-muted d-block mt-2">{{ __('front.current_photo') }}:
                                                                            {{ basename($me->photo) }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <fieldset>
                                                            <legend>{{ __('front.password_change') }}</legend>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label>{{ __('front.current_password') }}:</label>
                                                                    <input type="password" name="current_password">
                                                                    @error('current_password')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror

                                                                    <label>{{ __('front.new_password') }}:</label>
                                                                    <input type="password" name="new_password">
                                                                    @error('new_password')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror

                                                                    <label>{{ __('front.confirm_new_password') }}:</label>
                                                                    <input type="password"
                                                                        name="new_password_confirmation">
                                                                    @error('new_password_confirmation')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <div class="btn-wrapper">
                                                            <button type="submit"
                                                                class="btn theme-btn-1 btn-effect-1 text-uppercase">{{ __('front.save_changes') }}</button>
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
