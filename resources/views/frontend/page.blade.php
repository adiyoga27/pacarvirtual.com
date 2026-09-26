@extends('frontend.layout')

@section('content')
    @if($page->show_back_button)
        <div class="mt-3 pt-2">
            <div class="floating-button">
                <a href="{{ route('home') }}" class="btn btn-round" style="color: white;"><img src="{{ pv_asset('assets-legacy/images/back.png') }}" width="40px" alt="Kembali"></a>
            </div>
        </div>
    @endif

    <div id="preloader" style="display: none;">
        <div id="status" style="display: none;">
            <div class="spinner">Loading...</div>
        </div>
    </div>

    <div class="container" style="max-width: 680px; margin-top:50px" id="header-section">
        <div class="col-lg-12">
            @if($page->show_logo)
                <div class="mt-0">
                    @php $logoSrc = !empty($page->logo_path) ? pv_asset($page->logo_path) : pv_asset(($gsettings['logo'] ?? 'assets-legacy/logo.png')); @endphp
                    <img class="rounded-circle img-fluid mx-auto d-block" src="{{ $logoSrc }}" alt="{{ $page->header_title }}">
                </div>
            @endif
            <div class="header-content text-center mx-auto">
                <h4 class="custom-text-color-primary header-text mb-2 mt-3" style="color: white !important;">{{ $page->header_title }}</h4>
                @php
                    $typedOn = ($gsettings['typed_enabled'] ?? '1') === '1';
                    $typedPrefix = $gsettings['typed_prefix'] ?? 'Hidup ';
                    $typedStrings = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $gsettings['typed_strings'] ?? "Bahagia 💫\nPenuh Warna\nHilangi Ke Galauan 🔥"))));
                @endphp
                @if($page->subtitle)
                    <p class="custom-text-color-secondary subheader-text mb-2" style="color: white !important;">{{ $page->subtitle }}</p>
                @elseif($typedOn && count($typedStrings))
                    <p class="custom-text-color-secondary subheader-text mb-2" style="color: white !important;">{{ $typedPrefix }}<span class="element"></span></p>
                @endif
            </div>
        </div>
    </div>

    <div class="container" style="max-width: 680px" id="main-buttons">
        @forelse($page->activeButtons as $btn)
            <div class="mt-3 pt-2">
                <a href="{{ route('link.click', $btn->id) }}" @if($btn->open_new_tab) target="_blank" rel="noopener" @endif style="width: 100%" class="btn btn-round btn-custom">
                    @if($btn->icon_path)
                        <img src="{{ pv_asset($btn->icon_path) }}" width="{{ $btn->icon_width }}px" alt=""> &nbsp;
                    @endif
                    {{ $btn->title }}
                </a>
            </div>
        @empty
            <p class="text-center mt-4" style="color: white;">Belum ada tombol. Silakan tambah dari admin panel.</p>
        @endforelse
    </div>

    <div class="container" id="footer" style="margin-top: 50px;">
        <div class="col-md-12">
            <div class="mb-5 text-center">
                <div>
                    <hr style="color: white;">
                </div>
                <p class="custom-text-color-secondary footer-text mb-5 mt-3" style="color: white !important;">©{{ date('Y') }} - {{ $page->footer_text ?: ($gsettings['footer_text'] ?? 'Pacarvirtual.co_') }}</p>
            </div>
        </div>
    </div>
    <a href="#home" class="back_top" style="border-radius: 50%;"><i class="bi bi-chevron-up"></i></a>
@endsection

@push('scripts')
@if(($gsettings['typed_enabled'] ?? '1') === '1' && empty($page->subtitle))
<script>
    (function () {
        var el = document.querySelector('.element');
        if (el && typeof Typed !== 'undefined') {
            new Typed('.element', {
                strings: @json(array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', ($gsettings['typed_strings'] ?? "Bahagia 💫\nPenuh Warna\nHilangi Ke Galauan 🔥")))))),
                typeSpeed: 40,
                backSpeed: 40,
                backDelay: 1600,
                loop: true
            });
        }
    })();
</script>
<style type="text/css" data-typed-js-css="true">
    .typed-cursor { opacity: 1; }
    .typed-cursor.typed-cursor--blink {
        animation: typedjsBlink 0.7s infinite;
        -webkit-animation: typedjsBlink 0.7s infinite;
        animation: typedjsBlink 0.7s infinite;
    }
    @keyframes typedjsBlink { 50% { opacity: 0.0; } }
    @-webkit-keyframes typedjsBlink {
        0% { opacity: 1; }
        50% { opacity: 0.0; }
        100% { opacity: 1; }
    }
</style>
@endif
@endpush
