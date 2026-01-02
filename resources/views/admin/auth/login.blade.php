<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - Challenge Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-md p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Admin Login</h1>
                    <p class="text-gray-600 mt-2">Challenge Tracker Administration</p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        @foreach ($errors->all() as $error)
                            <span class="block">{{ $error }}</span>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.post') }}">
                    @csrf

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
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="admin@example.com"
                        >
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                            Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="••••••••"
                        >
                    </div>

                    <div class="mb-6">
                        <label class="inline-flex items-center">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            >
                            <span class="ml-2 text-gray-700 text-sm">Remember me</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <button
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                        >
                            Sign In
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-6 text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} Challenge Tracker. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
