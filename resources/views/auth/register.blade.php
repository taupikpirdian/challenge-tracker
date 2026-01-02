<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Challenge Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="mx-auto h-16 w-16 bg-amber-500 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2 5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1a3 3 0 00-3 3H6a3 3 0 00-3-3v-1a6 6 0 0112 0z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Register</h1>
                <p class="text-gray-600 mt-2">Buat akun participant baru</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    @foreach ($errors->all() as $error)
                        <span class="block">{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <!-- Register Form -->
            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                <!-- Name Field -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                        Nama Lengkap
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="John Doe"
                    >
                </div>

                <!-- Email Field -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                        Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="participant@example.com"
                    >
                </div>

                <!-- Phone Field (Optional) -->
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">
                        Nomor Handphone <span class="text-gray-500 font-normal">(Opsional)</span>
                    </label>
                    <input
                        id="phone"
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="08123456789"
                    >
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        minlength="8"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="••••••••"
                    >
                    <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">
                        Konfirmasi Password
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        minlength="8"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="••••••••"
                    >
                </div>

                <!-- Submit Button -->
                <div class="mb-4">
                    <button
                        type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                    >
                        Register
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-gray-600 text-sm">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-amber-500 hover:text-amber-600 font-bold">
                            Login
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
