<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Challenge Tracker - Track Your Progress & Achieve Your Goals')</title>
    <meta name="description" content="@yield('description', 'Join challenges, track your daily progress, and achieve your fitness goals with Challenge Tracker. Start your journey today!')">
    <meta name="keywords" content="@yield('keywords', 'challenge tracker, fitness challenges, goal tracking, progress tracking, daily challenges')">
    <meta name="author" content="@yield('author', 'Challenge Tracker')">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', 'Challenge Tracker - Track Your Progress')">
    <meta property="og:description" content="@yield('og_description', 'Join challenges and track your daily progress. Achieve your goals today!')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:site_name" content="Challenge Tracker">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="@yield('twitter_url', url()->current())">
    <meta property="twitter:title" content="@yield('twitter_title', 'Challenge Tracker - Track Your Progress')">
    <meta property="twitter:description" content="@yield('twitter_description', 'Join challenges and track your daily progress. Achieve your goals today!')">
    <meta property="twitter:image" content="@yield('twitter_image', asset('images/og-default.jpg'))">

    <!-- Additional SEO Meta Tags -->
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <meta name="googlebot" content="@yield('googlebot', 'index, follow')">
    <meta name="theme-color" content="#f59e0b">

    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                        }
                    }
                }
            }
        }
    </script>
    @stack('styles')

    <!-- JSON-LD Schema -->
    @yield('schema')
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ url('/dashboard') }}" class="flex items-center space-x-2">
                        <span class="text-2xl">🎯</span>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">Challenge Tracker</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="text-gray-700 dark:text-gray-300 hover:text-amber-600 dark:hover:text-amber-500 px-3 py-2 rounded-md text-sm font-medium transition">
                            Dashboard
                        </a>
                    @endauth
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-gray-700 dark:text-gray-300 text-sm">
                            {{ auth()->user()->name }}
                        </span>
                        <a href="{{ url('/dashboard/logout') }}"
                           class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-500 text-sm font-medium transition"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ url('/dashboard/logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12">
        <div class="container mx-auto px-4 py-6">
            <p class="text-center text-gray-600 dark:text-gray-400 text-sm">
                &copy; {{ date('Y') }} Challenge Tracker. All rights reserved.
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
