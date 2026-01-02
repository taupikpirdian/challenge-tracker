<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Challenge Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="mx-auto h-16 w-16 bg-amber-500 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Login</h1>
                <p class="text-gray-600 mt-2">Challenge Tracker</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    @foreach ($errors->all() as $error)
                        <span class="block">{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <!-- Email Field -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                        Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="participant@example.com"
                    >
                </div>

                <!-- Password Field -->
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="••••••••"
                    >
                </div>

                <!-- Remember Me -->
                <div class="mb-6">
                    <label class="inline-flex items-center">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50"
                        >
                        <span class="ml-2 text-gray-700 text-sm">Remember me</span>
                    </label>
                </div>

                <!-- Login Button -->
                <div class="mb-4">
                    <button
                        type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                    >
                        Login
                    </button>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <p class="text-gray-600 text-sm">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-amber-500 hover:text-amber-600 font-bold">
                            Register
                        </a>
                    </p>
                </div>
            </form>

            <!-- Footer -->
            <div class="text-center mt-6 text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} Challenge Tracker. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
