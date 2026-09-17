@php
    $framework = config('laravel-email-database-log-ui.framework', 'standalone');
    $theme = config('laravel-email-database-log.theme', 'system');
    $theme = in_array($theme, ['light', 'dark', 'system'], true) ? $theme : 'system';

@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-email-log-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title>@yield('title', 'Email log')</title>
    @if(config('laravel-email-database-log.stylesheet'))
        <link rel="stylesheet" href="{{ config('laravel-email-database-log.stylesheet') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('vendor/email-log/css/dashboard.css') }}">
    <script src="{{ asset('vendor/email-log/js/theme.js') }}" defer></script>
</head>
<body class="email-log">
    <a class="el-skip" href="#main">Skip to content</a>
    <header class="el-header">
        <div class="el-shell el-header-inner {{ $framework === 'bootstrap5' ? 'container' : ($framework === 'tailwind' ? 'mx-auto max-w-7xl px-6' : '') }}">
            <a class="el-brand" href="{{ route('email-log.index') }}"><span class="el-mark" aria-hidden="true">L</span> Email log</a>
            <div class="el-theme-control">
                <label for="el-theme">Appearance</label>
                <select id="el-theme" class="el-select" aria-label="Appearance">
                    <option value="system" @if($theme === 'system') selected @endif>System</option>
                    <option value="light" @if($theme === 'light') selected @endif>Light</option>
                    <option value="dark" @if($theme === 'dark') selected @endif>Dark</option>
                </select>
            </div>
        </div>
    </header>
    <main id="main" class="el-shell {{ $framework === 'bootstrap5' ? 'container' : ($framework === 'tailwind' ? 'mx-auto max-w-7xl px-6' : '') }}" tabindex="-1">
        @yield('content')
    </main>
    <footer class="el-shell el-footer">Laravel Email Database Log <span>Read-only mail history</span></footer>
</body>
</html>
