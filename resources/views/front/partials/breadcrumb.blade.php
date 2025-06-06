<!-- BREADCRUMB AREA START -->
<div class="ltn__breadcrumb-area text-left bg-overlay-white-30 bg-image "  data-bg="{{ asset("front/img/bg/14.jpg") }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ltn__breadcrumb-inner">
                    <h1 class="page-title">
                        @isset($title)
                            {{ $title }}
                        @endisset
                    </h1>
                    <div class="ltn__breadcrumb-list">
                        <ul>
                            @isset($breadcrumbs)
                                @foreach ($breadcrumbs as $breadcrumb)
                                @if ($loop->first)
                                    <li><a href="{{ $breadcrumb['link']?? ""}}"><span class="ltn__secondary-color"><i class="fas fa-home"></i></span> Home</a></li>
                                @endif
                                @if (!$loop->first)
                                    <li><a href="{{ $breadcrumb['link']?? ""}}">{{ $breadcrumb['name']?? "" }}</a></li>
                                @endif
                                @endforeach
                            @endisset
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- BREADCRUMB AREA END -->
