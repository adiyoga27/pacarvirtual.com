<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $gs = $gsettings ?? [];
        $pageMetaTitle = $page->meta_title ?? null;
        $siteName = $gs['site_name'] ?? 'PacarVirtual';
        $title = $pageMetaTitle ?: ($siteName . ' - ' . ($page->header_title ?? ''));
        $desc = $page->meta_description ?? ($gs['meta_description'] ?? 'Specialis Rental Pacar Online & Offline');
        $keywords = $page->meta_keywords ?? ($gs['meta_keywords_default'] ?? 'sewa pacar, rental pacar');
        $author = $page->meta_author ?? ($gs['meta_author'] ?? 'https://pacarvirtual.com/');
        $ogImage = !empty($page->og_image) ? pv_asset($page->og_image) : pv_asset($gs['og_image_default'] ?? 'assets-legacy/logo.png');
        $favicon = pv_asset($gs['favicon'] ?? 'assets-legacy/images/icon.png');
        $gtmId = $gs['gtm_id'] ?? '';
        $gtagId = $gs['gtag_id'] ?? '';
        $fontFamily = $gs['font_family'] ?? '"Nunito", sans-serif';
        $textColor = $gs['text_color'] ?? '#ffffff';
        // Background: prioritas page jika tidak pakai global
        if (!($page->use_global_background ?? true)) {
            $bgType = $page->background_type ?? 'image';
            $bgImage = !empty($page->background_image) ? pv_asset($page->background_image) : '';
            $bgColor = $page->background_color ?? '#1a0b2e';
            $bgGradient = $page->background_gradient ?? '';
        } else {
            $bgType = $gs['background_type'] ?? 'image';
            $bgImage = pv_asset($gs['background_image'] ?? 'assets-legacy/background.png');
            $bgColor = $gs['background_color'] ?? '#1a0b2e';
            $bgGradient = $gs['background_gradient'] ?? '';
        }
        $logoSrc = !empty($page->logo_path) ? pv_asset($page->logo_path) : pv_asset($gs['logo'] ?? 'assets-legacy/logo.png');
        // Warna tombol default disamakan dengan situs live (#c76e91 dari light-mode.css)
        $primaryColor = $gs['primary_color'] ?? '#c76e91';
        $btnRadius = (int) ($gs['button_radius'] ?? 30);
    @endphp
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $desc }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="author" content="{{ $author }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <link rel="shortcut icon" href="{{ $favicon }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets-legacy/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets-legacy/css/animate.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets-legacy/css/style.css') }}">
    <style>
        :root {
            --pv-primary: {{ $primaryColor }};
            --pv-radius: {{ $btnRadius }}px;
        }
        body {
            font-family: {!! $fontFamily !!};
            @if($bgType === 'image' && $bgImage)
                background-image: url("{{ $bgImage }}");
                background-size: cover;
                background-position: top center;
            @elseif($bgType === 'gradient' && $bgGradient)
                background: {{ $bgGradient }};
            @else
                background-color: {{ $bgColor }};
            @endif
            min-height: 100vh;
            margin: 0 !important;
            color: {{ $textColor }};
        }
        .btn {
            text-align: start !important;
            display: flex;
            justify-content: center; /* Mengatur posisi horizontal ke tengah */
            align-items: center; /* Mengatur posisi vertikal ke tengah */
            font-weight: bold;
        }
        .floating-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 9999;
            display: flex;
        }
    </style>
    @if($gtmId)
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
    @endif
    @if(!empty($gs['custom_head'])) {!! $gs['custom_head'] !!} @endif
</head>
<body style="overflow: visible;">
    @yield('content')
    <script src="{{ asset('assets-legacy/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets-legacy/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets-legacy/js/typed.js') }}"></script>
    <script src="{{ asset('assets-legacy/js/custom.js') }}"></script>
    @stack('scripts')
    @if($gtagId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gtagId }}"></script>
    <script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '{{ $gtagId }}');</script>
    @endif
    <link id="style1" rel="stylesheet" type="text/css" href="{{ asset('assets-legacy/css/colors/light-mode.css') }}">
    <style>
        /* Override dinamis dari admin — ditaruh SETELAH light-mode.css agar menang
           tanpa !important, sehingga animasi hover (transisi 0.5s ke putih) tetap hidup persis situs live */
        .btn-custom {
            background-color: var(--pv-primary);
            border-color: var(--pv-primary);
            color: #fff;
            border-radius: var(--pv-radius);
        }
        .btn-custom:hover,
        .btn-custom:focus,
        .btn-custom:active,
        .btn-custom.active,
        .btn-custom.focus,
        .open > .dropdown-toggle.btn-custom {
            color: rgb(61, 59, 60);
            border: 1px solid rgb(61, 59, 60);
            background-color: #fff;
        }
    </style>
    @if(!empty($gs['custom_body'])) {!! $gs['custom_body'] !!} @endif
</body>
</html>
