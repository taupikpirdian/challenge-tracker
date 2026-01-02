<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Challenge Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="h-10 w-10 bg-amber-500 rounded-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h1 class="ml-3 text-xl font-bold text-gray-900">Challenge Tracker</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('participant.dashboard') }}" class="border-b-2 border-amber-500 text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">
                            Dashboard
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-700 mr-4">Halo, {{ $user->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="py-10">
        <main>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Selamat Datang, {{ $user->name }}!</h2>

                            <!-- User Info -->
                            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Informasi Akun</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Email</p>
                                        <p class="font-medium text-gray-900">{{ $user->email }}</p>
                                    </div>
                                    @if($user->phone)
                                    <div>
                                        <p class="text-sm text-gray-600">No HP</p>
                                        <p class="font-medium text-gray-900">{{ $user->phone }}</p>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="text-sm text-gray-600">Role</p>
                                        <p class="font-medium text-gray-900">{{ $user->roles->pluck('name')->first() ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Terdaftar Sejak</p>
                                        <p class="font-medium text-gray-900">{{ $user->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-blue-50 rounded-lg p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-blue-600">Challenges</p>
                                            <p class="text-2xl font-semibold text-gray-900">-</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 rounded-lg p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001-.946-3.97 3.42 3.42 0 00-3.633-3.563 3.42 3.42 0 00-3.416 3.614 3.42 3.42 0 00-5.586 2.357 3.42 3.42 0 00-2.012 5.636 3.42 3.42 0 00.846 3.96 3.42 3.42 0 013.933 3.47 3.42 3.42 0 014.657-3.47 3.42 3.42 0 00.846-3.96 3.42 3.42 0 005.586-2.357 3.42 3.42 0 003.416-3.614 3.42 3.42 0 003.633 3.563 3.42 3.42 0 001.946 3.97" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-green-600">Submissions</p>
                                            <p class="text-2xl font-semibold text-gray-900">-</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-purple-50 rounded-lg p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-purple-600">Activity</p>
                                            <p class="text-2xl font-semibold text-gray-900">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Coming Soon -->
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Fitur challenges dan submissions akan segera tersedia!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
