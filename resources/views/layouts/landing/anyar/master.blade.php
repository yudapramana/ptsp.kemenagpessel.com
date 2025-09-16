<!DOCTYPE html>
<html lang="en">

<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6641141866403336" crossorigin="anonymous"></script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LTPQ4EM2TL"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-LTPQ4EM2TL');
    </script>

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>@yield('title')</title>
    <meta name="description" content="PTSP KemenagPessel - PTSP Kementerian Agama Kabupaten Pesisir Selatan" />
    <meta name="keywords" content="PTSP, PTSP ONLINE, Pelayanan Publik" />
    <meta name="author" content="Pramana Yuda Sayeti" />
    <meta name="robots" content="all" />
    <meta name="revisit-after" content="1 Days" />
    <meta property="og:locale" content="id-ID" />
    <meta property="og:site_name" content="PTSP ONLINE KEMENAGPESSEL" />
    <meta property="og:image" content="https://pesisirselatan.kemenag.go.id/v1/uploads/logo/logo_5f315af825f0d.png" />
    <meta property="og:image:width" content="180" />
    <meta property="og:image:height" content="50" />
    <meta property="og:type" content=website />
    <meta property="og:title" content="PTSP ONLINE - Kemenag Pesisir Selatan" />
    <meta property="og:description" content="Pelayanan Publik Terpadu Satu Pintu Berbasis Web (ONLINE) Kementerian Agama Kabupaten Pesisir Selatan" />
    <meta property="og:url" content="https://ptsp.kemenagpessel.com" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="PTSP ONLINE Kemenag Pesisir Selatan" />
    <meta name="twitter:title" content="PTSP ONLINE - Kemenag Pesisir Selatan" />
    <meta name="twitter:description" content="Pelayanan Publik Terpadu Satu Pintu Berbasis Web (ONLINE) Kementerian Agama Kabupaten Pesisir Selatan" />
    <meta name="twitter:image" content="https://pesisirselatan.kemenag.go.id/v1/uploads/logo/logo_5f315af825f0d.png" />
    <link rel="canonical" href="https://ptsp.kemenagpessel.com" />
    <link rel="alternate" hreflang="en-US" href="https://ptsp.kemenagpessel.com" />
    <link rel="shortcut icon" type="image/png" href="https://pesisirselatan.kemenag.go.id/v1/uploads/logo/logo_5f315af825f0d2.png" />

    @include('layouts.landing.anyar.styles')

    @yield('_styles')



    <!-- =======================================================
  * Template Name: Anyar - v4.9.0
  * Template URL: https://bootstrapmade.com/anyar-free-multipurpose-one-page-bootstrap-theme/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="d-flex flex-column min-vh-100">

    @include('layouts.landing.anyar.header')

    <main id="main">

        @yield('content')

    </main><!-- End #main -->

    @include('layouts.landing.anyar.footer')
    @include('layouts.landing.anyar.scripts')


    @yield('_scripts')


    <script defer>
        document.addEventListener('load', function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "https://widget.equally.ai/equally-widget.min.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'equallyWidget'));
        !window.EQUALLY_AI_API_KEY && (window.EQUALLY_AI_API_KEY = "HUEjaXltfIEVyLgfE3kO", intervalId = setInterval(function() {
            window.EquallyAi && (clearInterval(intervalId), window.EquallyAi = new EquallyAi)
        }, 500));
    </script>

    <script src="https://code.responsivevoice.org/responsivevoice.js?key=zJWb4A6g"></script>
</body>

</html>
