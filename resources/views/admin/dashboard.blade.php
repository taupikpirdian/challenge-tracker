<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Challenge Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Challenge Tracker</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('admin.dashboard') }}" class="border-b-2 border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">
                            Dashboard
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-700 mr-4">Welcome, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-10">
        <main>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Admin Dashboard</h2>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                <div class="bg-blue-50 rounded-lg p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-blue-600">Total Users</p>
                                            <p class="text-2xl font-semibold text-gray-900">-</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 rounded-lg p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-green-600">Active Challenges</p>
                                            <p class="text-2xl font-semibold text-gray-900">-</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-purple-50 rounded-lg p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-purple-600">Total Submissions</p>
                                            <p class="text-2xl font-semibold text-gray-900">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Welcome to the Admin Dashboard</h3>
                                <p class="text-gray-600">
                                    This is the admin dashboard for Challenge Tracker. Use this interface to manage challenges,
                                    participants, and submissions. More features will be added soon.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
