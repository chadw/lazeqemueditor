<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Login') - LazEQEmu Editor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    @stack('head')
</head>
<body class="min-h-screen bg-base-300 text-base-content antialiased">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full">
            <div class="text-center mb-6">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <img src="/images/logo.png" alt="Logo" class="w-full rounded">
                </a>
            </div>

            <div class="card bg-base-200 shadow-lg">
                <div class="card-body">
                    <h2 class="text-2xl font-semibold mb-2">Sign in to your account</h2>
                    <p class="text-sm text-muted mb-4">Enter your credentials to access the editor.</p>

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
