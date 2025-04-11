@extends('front.app')
@section('styles')
@endsection
@section('content')
    @include('front.partials.breadcrumb')

    <!-- PAGE DETAILS AREA START (portfolio-details) -->
    <div class="ltn__page-details-area ltn__portfolio-details-area mb-105">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__page-details-inner ltn__portfolio-details-inner">
                        <div class="ltn__blog-img">
                            <img src="{{ $event->getImage() }}" alt="Image">
                        </div>
                        <table>
                            <tr>
                                <td><strong>Dari Tanggal:</strong></td>
                                <td>{{ Carbon\Carbon::parse($event->start)->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Hingga Tanggal:</strong></td>
                                <td>{{ Carbon\Carbon::parse($event->end)->format('d M Y') }}</td>
                            </tr>
                        </table>
                        <p>
                            {!! $event->content !!}
                        </p>
                        @if ($event->file)
                        <object data="{{ Storage::url($event->file) }}" type="application/pdf"
                            width="100%" height="800px">
                            <embed src="{{ Storage::url($event->file) }}" type="application/pdf" />
                        </object>
                    @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- PAGE DETAILS AREA END -->
@endsection
