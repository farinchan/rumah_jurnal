<div class="page-title d-flex justify-content-center flex-column me-5">
    @isset($title)
        <h1 class="d-flex flex-column text-gray-900 fw-bold fs-3 mb-0">{{ $title }} </h1>
    @endisset
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
        <li class="breadcrumb-item text-muted">
            <i class="ki-duotone ki-home text-gray-400"></i>
        </li>
        @isset($breadcrumbs)
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-300 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb['link']?? ""}}" class="text-muted text-hover-primary @if ($loop->last) text-gray-800 @endif">
                        {{ $breadcrumb['name']?? "" }} </a>
                </li>
            @endforeach
        @endisset
    </ul>
</div>
