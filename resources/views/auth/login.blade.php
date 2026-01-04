<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Challenge Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            animation: slideIn 0.3s ease-out;
        }
        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #34d399;
            color: #065f46;
        }
        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #f87171;
            color: #991b1b;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .btn-loading {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
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

            <!-- Alert Container -->
            <div id="alert-container"></div>

            <!-- Login Form -->
            <form id="login-form">
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
                        id="login-btn"
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

    <script>
        $(document).ready(function() {
            $('#login-form').on('submit', function(e) {
                e.preventDefault();

                // Remove existing alerts
                $('#alert-container').empty();

                // Add loading state
                const $btn = $('#login-btn');
                const originalText = $btn.text();
                $btn.addClass('btn-loading').text('Logging in...');

                // Get form data
                const formData = $(this).serialize();

                // Send AJAX request
                $.ajax({
                    url: '{{ route('login.post') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            showAlert('Login successful! Redirecting...', 'success');

                            // Redirect to dashboard
                            setTimeout(function() {
                                window.location.href = response.redirect || '/dashboard';
                            }, 500);
                        } else {
                            // Show error message
                            showAlert(response.message || 'Login failed. Please try again.', 'error');
                            $btn.removeClass('btn-loading').text(originalText);
                        }
                    },
                    error: function(xhr) {
                        // Handle error response
                        let errorMessage = 'Login failed. Please try again.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }

                        showAlert(errorMessage, 'error');
                        $btn.removeClass('btn-loading').text(originalText);
                    }
                });
            });

            function showAlert(message, type) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
                const alertHtml = '<div class="alert ' + alertClass + '">' + message + '</div>';
                $('#alert-container').html(alertHtml);

                // Auto-remove success alerts after 3 seconds
                if (type === 'success') {
                    setTimeout(function() {
                        $('#alert-container').empty();
                    }, 3000);
                }
            }
        });
    </script>
</body>
</html>
