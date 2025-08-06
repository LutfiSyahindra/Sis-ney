    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Responsive HTML Admin Dashboard Template based on Bootstrap 5">
    <meta name="author" content="NobleUI">
    <meta name="keywords"
        content="nobleui, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <title>{{ "SISNEY | " . ($pageTitle ?? "SISNEY") }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- End fonts -->

    {{-- @vite(["resources/css/app.css", "resources/js/app.js"])

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset("build/manifest.webmanifest") }}">
    <meta name="theme-color" content="#0d6efd"> --}}

    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset("template/assets/vendors/core/core.css") }}">
    <!-- endinject -->

    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset("template/assets/vendors/flatpickr/flatpickr.min.css") }}">
    <!-- End plugin css for this page -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset("template/assets/fonts/feather-font/css/iconfont.css") }}">
    <link rel="stylesheet" href="{{ asset("template/assets/vendors/flag-icon-css/css/flag-icon.min.css") }}">
    <!-- endinject -->

    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset("template/assets/css/demo1/style.css") }}">
    <!-- End layout styles -->

    <link rel="shortcut icon" href="{{ asset("template/assets/images/favicon.png") }}" />

    <link rel="stylesheet" href="{{ asset("template/assets/fonts/feather-font/css/iconfont.css") }}">
    <link rel="stylesheet" href="{{ asset("template/assets/vendors/flag-icon-css/css/flag-icon.min.css") }}">

    <link rel="stylesheet" href="{{ asset("template/assets/vendors/prismjs/themes/prism.css") }}">

    <!-- PWA manifest dan theme-color -->

    @vite(["resources/js/pwa.js"])
    <link rel="manifest" href="{{ asset("build/manifest.webmanifest") }}">
    <meta name="theme-color" content="#0d6efd">
