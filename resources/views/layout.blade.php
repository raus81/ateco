<!doctype html>
<html lang="it" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

@stack('head')
<!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Bootstrap core CSS -->
    <link href="/css/app.css" rel="stylesheet">

{{--    <!-- Favicons -->--}}
{{--    <link rel="apple-touch-icon" href="/docs/5.0/assets/img/favicons/apple-touch-icon.png" sizes="180x180">--}}
{{--    <link rel="icon" href="/docs/5.0/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">--}}
{{--    <link rel="icon" href="/docs/5.0/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">--}}
{{--    <link rel="manifest" href="/docs/5.0/assets/img/favicons/manifest.json">--}}
{{--    <link rel="mask-icon" href="/docs/5.0/assets/img/favicons/safari-pinned-tab.svg" color="#7952b3">--}}
{{--    <link rel="icon" href="/docs/5.0/assets/img/favicons/favicon.ico">--}}
    <meta name="theme-color" content="#7952b3">


    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>



</head>
<body class="d-flex flex-column h-100">

<header>
    <!-- Fixed navbar -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/"><img width="119" height="40" alt="Logo ateco.numerosamente.it" src="/imgs/logoAteco.svg"/></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link  {{ (Request::is('/') ? 'active' : '') }}" aria-current="page" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ (Request::is('about-us') ? 'active' : '') }}" href="/about-us">Chi siamo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ (Request::is('faq') ? 'active' : '') }}" href="/faq" tabindex="-1"  >FAQ</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Informazioni utili
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/errori-comuni">Errori comuni</a></li>
                            <li><a class="dropdown-item" href="/esempi-utilizzo">Esempi di utlizzo</a></li>
                            <li><a class="dropdown-item" href="/casi-di-studio">Casi di studio</a></li>
                            <li><a class="dropdown-item" href="/codice-corretto">Scegliere il codice Ateco</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/glossario">Glossario</a></li>

                        </ul>
                    </li>
                </ul>

            </div>
        </div>
    </nav>
</header>

<!-- Begin page content -->
<main class="flex-shrink-0 flex-grow-1 d-flex">
    <div class="container flex-grow-1">
         @yield('content')


    </div>
</main>

<footer class="footer mt-auto py-3 bg-dark">
    <div class="container">
        <span class="text-white">ateco.numerosamente.it &copy; {{date('Y')}}</span>
    </div>
</footer>


@yield('script')
</body>
</html>
