<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'Ochoka Heritage') }}</title>
    <meta name="description" content="Ochoka Heritage is a modern community platform for membership, welfare, contributions, governance, elections, meetings, and preserving institutional heritage.">
    <meta name="keywords" content="Ochoka Heritage, community management, community welfare, member contributions, community governance, elections, meetings, membership management, welfare fund, institutional heritage">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Ochoka Heritage">
    <link rel="canonical" href="{{ url('/') }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name', 'Ochoka Heritage') }}">
    <meta property="og:title" content="Ochoka Heritage | Community, Welfare & Governance">
    <meta property="og:description" content="A modern digital platform connecting membership, welfare, contributions, governance, elections, meetings, and institutional heritage.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Ochoka Heritage | Community, Welfare & Governance">
    <meta name="twitter:description" content="A modern digital platform for community membership, welfare, contributions, governance, elections, meetings, and institutional heritage.">
    <meta name="theme-color" content="#ffffff">
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    @inertiaHead
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name', 'Ochoka Heritage'),
            'url' => url('/'),
            'description' => 'A modern community platform for membership, welfare, contributions, governance, elections, meetings, and institutional heritage.',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
</head>
<body class="bg-white antialiased text-slate-900">
    @inertia
</body>
</html>
