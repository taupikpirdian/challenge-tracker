@extends('layouts.app')

@php
    // Generate dynamic metadata based on challenge data
    $metaTitle = $challenge->title . ' - ' . ($challenge->status === 'active' ? 'Active Challenge' : 'Challenge') . ' | Challenge Tracker';
    $metaDescription = \Illuminate\Support\Str::limit(
        strip_tags($challenge->description ?: "Join {$challenge->title}, a {$challenge->duration_days}-day challenge. Start: {$challenge->start_date->format('M d')}, End: {$challenge->end_date->format('M d, Y')}"),
        160
    );

    // Generate keywords from challenge data
    $metaKeywords = collect([
        $challenge->title,
        'challenge',
        $challenge->duration_days . ' day challenge',
        $challenge->status,
        'fitness challenge',
        'progress tracking',
        $challenge->creator?->name,
        $challenge->start_date->format('F Y'),
    ])->filter()->unique()->implode(', ');

    // Count participants
    $participantsCount = $challenge->participants_count ?? $challenge->participants()->count();

    // Generate og description with more context
    $ogDescription = "🎯 {$challenge->title}\n";
    $ogDescription .= "📅 {$challenge->start_date->format('M d')} - {$challenge->end_date->format('M d, Y')}\n";
    $ogDescription .= "⏱️ {$challenge->duration_days} days\n";
    if ($participantsCount > 0) {
        $participantLabel = $participantsCount === 1 ? 'participant' : 'participants';
        $ogDescription .= "👥 {$participantsCount} {$participantLabel}\n";
    }
    if ($challenge->description) {
        $ogDescription .= "\n" . \Illuminate\Support\Str::limit(strip_tags($challenge->description), 100);
    }
@endphp

@section('title')
    {{ $metaTitle }}
@endsection

@section('description')
    {{ $metaDescription }}
@endsection

@section('keywords')
    {{ $metaKeywords }}
@endsection

@section('author')
    {{ $challenge->creator?->name ?? 'Challenge Tracker' }}
@endsection

@section('og_title')
    {{ $challenge->title }}
@endsection

@section('og_description')
    {{ $ogDescription }}
@endsection

@section('og_image')
    {{ $challenge->cover_image ? asset('storage/' . $challenge->cover_image) : asset('images/og-default.jpg') }}
@endsection

@section('og_url')
    {{ url()->current() }}
@endsection

@section('twitter_title')
    {{ $challenge->title }}
@endsection

@section('twitter_description')
    {{ $ogDescription }}
@endsection

@section('twitter_image')
    {{ $challenge->cover_image ? asset('storage/' . $challenge->cover_image) : asset('images/og-default.jpg') }}
@endsection

@section('twitter_url')
    {{ url()->current() }}
@endsection

@section('canonical')
    {{ url()->current() }}
@endsection

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-6 flex items-center">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6 flex items-center">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-red-800 dark:text-red-300">{{ session('error') }}</p>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6 flex items-center">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-blue-800 dark:text-blue-300">{{ session('info') }}</p>
        </div>
    @endif

    <!-- Back Button -->
    <a href="{{ url()->previous() }}" class="inline-flex items-center text-amber-600 hover:text-amber-700 mb-6">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Challenges
    </a>

    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
        @if($challenge->cover_image)
            <div class="h-64 md:h-80 overflow-hidden">
                <img src="{{ asset('storage/' . $challenge->cover_image) }}"
                     alt="{{ $challenge->title }}"
                     class="w-full h-full object-cover">
            </div>
        @else
            <div class="h-64 md:h-80 bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                <span class="text-white text-6xl font-bold opacity-50">🎯</span>
            </div>
        @endif

        <div class="p-6 md:p-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">
                    {{ $challenge->title }}
                </h1>
                @php
                    $statusClasses = match($challenge->status) {
                        'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                        'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                        default => 'bg-gray-100 text-gray-800'
                    };
                @endphp
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $statusClasses }}">
                    {{ ucfirst($challenge->status) }}
                </span>
            </div>

            @if($challenge->description)
                <div class="prose dark:prose-invert max-w-none mb-6">
                    {!! $challenge->description !!}
                </div>
            @endif

            <!-- Share Challenge Section -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Share this challenge:</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="shareChallengeToWhatsApp()" class="flex items-center gap-1 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition shadow-sm" title="Share to WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </button>

                        <button onclick="copyChallengeLink()" class="flex items-center gap-1 px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition shadow-sm" title="Copy Link">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>

                        <button onclick="shareChallengeToFacebook()" class="flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm" title="Share to Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </button>

                        <button onclick="shareChallengeToTwitter()" class="flex items-center gap-1 px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium rounded-lg transition shadow-sm" title="Share to Twitter">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Challenge Meta -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                    <div class="text-2xl mb-1">📅</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Start Date</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                        {{ $challenge->start_date->format('M d, Y') }}
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                    <div class="text-2xl mb-1">🏁</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">End Date</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                        {{ $challenge->end_date->format('M d, Y') }}
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                    <div class="text-2xl mb-1">⏱️</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Duration</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                        {{ $challenge->duration_days }} days
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                    <div class="text-2xl mb-1">👤</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Created By</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                        {{ $challenge->creator?->name ?? 'System' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Challenge Stats Section -->
    @if($challenge->status === 'active' || $challenge->status === 'completed')
        <div class="bg-gradient-to-r from-amber-50 via-orange-50 to-yellow-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 rounded-xl shadow-lg p-6 md:p-8 mb-8 border border-amber-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                Challenge Statistics
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Active Streaks -->
                <div class="bg-gradient-to-br from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 rounded-xl p-5 border border-orange-300 dark:border-orange-700 transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center shadow-lg">
                            <span class="text-2xl">🔥</span>
                        </div>
                        <span class="text-xs font-semibold text-orange-700 dark:text-orange-300 bg-orange-200 dark:bg-orange-800 px-2 py-1 rounded-full">
                            Active
                        </span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $activeStreakCount ?? 0 }}
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                        peoples keep the streak
                    </p>
                </div>

                <!-- New Submissions -->
                <div class="bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 rounded-xl p-5 border border-green-300 dark:border-green-700 transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg">
                            <span class="text-2xl">🆕</span>
                        </div>
                        <span class="text-xs font-semibold text-green-700 dark:text-green-300 bg-green-200 dark:bg-green-800 px-2 py-1 rounded-full">
                            New
                        </span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $newSubmissionsCount ?? 0 }}
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                        new submission
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                        Last 24 hours
                    </p>
                </div>

                <!-- Left Behind -->
                <div class="bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-xl p-5 border border-blue-300 dark:border-blue-700 transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                            <span class="text-2xl">😔</span>
                        </div>
                        <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 bg-blue-200 dark:bg-blue-800 px-2 py-1 rounded-full">
                            Alert
                        </span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $leftBehindCount ?? 0 }}
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                        people left behind
                    </p>
                </div>

                <!-- Total Submissions -->
                <div class="bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 rounded-xl p-5 border border-purple-300 dark:border-purple-700 transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                            <span class="text-2xl">📊</span>
                        </div>
                        <span class="text-xs font-semibold text-purple-700 dark:text-purple-300 bg-purple-200 dark:bg-purple-800 px-2 py-1 rounded-full">
                            Total
                        </span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $totalSubmissions ?? 0 }}
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                        total submissions
                    </p>
                </div>
            </div>

            <!-- Progress Bar for Challenge Completion -->
            @if($challenge->duration_days > 0 && $challenge->start_date && $challenge->end_date)
                @php
                    $totalChallengeDays = $challenge->start_date->diffInDays($challenge->end_date) + 1;
                    $daysElapsed = $challenge->start_date->diffInDays(now()) + 1;
                    $challengeProgress = min(100, max(0, round(($daysElapsed / $totalChallengeDays) * 100)));
                @endphp
                <div class="mt-6 pt-6 border-t border-gray-300 dark:border-gray-600">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Challenge Time Progress
                        </span>
                        <span class="text-sm font-bold text-amber-600 dark:text-amber-400">
                            {{ $challengeProgress }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 h-4 rounded-full transition-all duration-700 ease-out relative"
                             style="width: {{ $challengeProgress }}%">
                            <div class="absolute inset-0 bg-white opacity-20 animate-pulse"></div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 text-center">
                        Day {{ min($daysElapsed, $totalChallengeDays) }} of {{ $totalChallengeDays }}
                    </p>
                </div>
            @endif
        </div>
    @endif

    <!-- Feed/Timeline Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 md:p-8 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Activity Feed
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mt-2">See all submissions from challenge participants</p>
        </div>

        <!-- Vertical Scroll Container with Fixed Height -->
        <div class="overflow-y-auto" style="max-height: 800px;">
            <div class="max-w-2xl mx-auto">
                @if($feedSubmissions->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($feedSubmissions as $submission)
                            <!-- Feed Post -->
                            <div class="p-6 md:p-8">
                                <!-- Post Header -->
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <!-- User Avatar -->
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                            {{ strtoupper(substr($submission->user->name ?? 'A', 0, 1)) }}
                                        </div>

                                        <div>
                                            <!-- Username -->
                                            <h4 class="font-semibold text-gray-900 dark:text-white">
                                                {{ $submission->user->name ?? 'Anonymous' }}
                                                @if($submission->user && $submission->user->id === auth()->id())
                                                    <span class="text-xs bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300 px-2 py-0.5 rounded-full ml-2">
                                                        You
                                                    </span>
                                                @endif
                                            </h4>

                                            <!-- Time & Status -->
                                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                                <span>{{ $submission->time_ago }}</span>
                                                <span>•</span>
                                                <span>Day {{ \Carbon\Carbon::createFromTimestamp($submission->day_number)->format('d') }}</span>
                                                <span>•</span>
                                                @php
                                                    $statusColors = match($submission->status) {
                                                        'approved' => 'text-green-600',
                                                        'rejected' => 'text-red-600',
                                                        default => 'text-yellow-600',
                                                    };
                                                @endphp
                                                <span class="{{ $statusColors }} font-medium">
                                                    {{ ucfirst($submission->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- More Options Button -->
                                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Submission Content -->
                                @if($submission->values && $submission->values->count() > 0)
                                    <div class="space-y-4 mb-4">
                                    @foreach($submission->values as $value)
                                        @if($value->rule)
                                            <!-- Image/Video Content -->
                                            @if($value->rule->field_type === 'image' && $value->value_text)
                                                <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-md">
                                                    <img src="{{ asset('storage/' . $value->value_text) }}"
                                                         alt="{{ $value->rule->label }}"
                                                         class="w-full max-h-96 object-cover"
                                                         onclick="openImageModal('{{ asset('storage/' . $value->value_text) }}')"
                                                         onerror="this.parentElement.style.display='none'">
                                                </div>

                                            <!-- File Content -->
                                            @elseif($value->rule->field_type === 'file' && $value->value_text)
                                                @php
                                                    $filePath = $value->value_text;
                                                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
                                                @endphp
                                                @if($isImage)
                                                    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-md">
                                                        <img src="{{ asset('storage/' . $value->value_text) }}"
                                                             alt="{{ $value->rule->label }}"
                                                             class="w-full max-h-96 object-cover"
                                                             onclick="openImageModal('{{ asset('storage/' . $value->value_text) }}')"
                                                             onerror="this.parentElement.style.display='none'">
                                                    </div>
                                                @else
                                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            <div>
                                                                <p class="font-medium text-gray-900 dark:text-white">{{ $value->rule->label }}</p>
                                                                <a href="{{ asset('storage/' . $value->value_text) }}"
                                                                   target="_blank"
                                                                   class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 text-sm flex items-center mt-1">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                                    </svg>
                                                                    Download file
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                            <!-- Text/Number/Other Content -->
                                            @elseif(in_array($value->rule->field_type, ['text', 'textarea', 'number', 'date', 'time', 'datetime', 'select', 'radio']) && ($value->value_text || $value->value_number !== null))
                                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">{{ $value->rule->label }}</p>
                                                    <p class="text-gray-900 dark:text-white">
                                                        @if($value->rule->field_type === 'textarea')
                                                            <span class="whitespace-pre-wrap">{{ $value->value_text }}</span>
                                                        @elseif($value->rule->field_type === 'number')
                                                            {{ $value->value_number ?? $value->value_text }}
                                                        @else
                                                            {{ $value->value_text }}
                                                        @endif
                                                    </p>
                                                </div>

                                            <!-- Boolean/Toggle/Checkbox -->
                                            @elseif(in_array($value->rule->field_type, ['checkbox', 'toggle']) && $value->value_boolean !== null)
                                                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $value->rule->label }}:</span>
                                                    @if($value->value_boolean)
                                                        <span class="text-green-600 font-semibold flex items-center gap-1">
                                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Yes
                                                        </span>
                                                    @else
                                                        <span class="text-red-600 font-semibold flex items-center gap-1">
                                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            No
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <!-- Engagement Bar -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-6">
                                    <!-- Like Button -->
                                    <button class="flex items-center gap-2 text-gray-600 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400 transition group">
                                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Like</span>
                                    </button>

                                    <!-- Comment Button -->
                                    <button class="flex items-center gap-2 text-gray-600 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-400 transition group">
                                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Comment</span>
                                    </button>

                                    <!-- Share Button -->
                                    <button class="flex items-center gap-2 text-gray-600 hover:text-green-500 dark:text-gray-400 dark:hover:text-green-400 transition group">
                                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Share</span>
                                    </button>
                                </div>

                                <!-- Save Button -->
                                <button class="text-gray-600 hover:text-amber-500 dark:text-gray-400 dark:hover:text-amber-400 transition group">
                                    <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Timestamp -->
                            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                Submitted {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y \a\t g:i A') : 'recently' }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Empty State -->
                    <div class="p-12 text-center">
                        <svg class="w-24 h-24 mx-auto text-gray-300 dark:text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">No Activity Yet</h3>
                        <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                            Be the first to share your progress! Once participants start submitting, you'll see their updates here.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>


    <!-- Progress Tabs Section -->
    @if($isParticipant && $challenge->status === 'active')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
            <!-- Tabs Header -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex -mb-px">
                    <button type="button" onclick="switchTab('update-progress')"
                            id="tab-update-progress"
                            class="tab-button flex-1 py-4 px-6 text-center font-semibold border-b-2 border-amber-500 text-amber-600 dark:text-amber-400">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Update Progress Harian
                    </button>
                    <button type="button" onclick="switchTab('history-progress')"
                            id="tab-history-progress"
                            class="tab-button flex-1 py-4 px-6 text-center font-semibold border-b-2 border-transparent text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        History Progress
                    </button>
                    <button type="button" onclick="switchTab('top-10')"
                            id="tab-top-10"
                            class="tab-button flex-1 py-4 px-6 text-center font-semibold border-b-2 border-transparent text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Board Top 10
                    </button>
                </nav>
            </div>

            <!-- Tab Content: Update Progress Harian -->
            <div id="content-update-progress" class="tab-content p-6 md:p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Update Progress Harian</h3>

                @if($challenge->rules && $challenge->rules->count() > 0)
                    <form method="POST" action="{{ route('submissions.store', $challenge->id) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Date Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tanggal Progress
                            </label>
                            <input type="text"
                                   id="submission-datepicker"
                                   name="submission_date"
                                   required
                                   placeholder="Pilih tanggal..."
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white cursor-pointer">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Periode: {{ $challenge->start_date->format('M d, Y') }} - {{ $challenge->end_date->format('M d, Y') }}
                            </p>
                        </div>

                        <!-- Dynamic Form Fields -->
                        @foreach($challenge->rules->sortBy('order_number') as $rule)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ $rule->label }}
                                    @if($rule->is_required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                @if($rule->field_type === 'text')
                                    <input type="text" name="fields[{{ $rule->id }}]"
                                           {{ $rule->is_required ? 'required' : '' }}
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                           placeholder="Enter {{ $rule->label }}">

                                @elseif($rule->field_type === 'number')
                                    <input type="number" name="fields[{{ $rule->id }}]" step="any"
                                           {{ $rule->is_required ? 'required' : '' }}
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                           placeholder="Enter {{ $rule->label }}">

                                @elseif($rule->field_type === 'textarea')
                                    <textarea name="fields[{{ $rule->id }}]" rows="3"
                                              {{ $rule->is_required ? 'required' : '' }}
                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                              placeholder="Enter {{ $rule->label }}"></textarea>

                                @elseif($rule->field_type === 'date')
                                    <div class="relative">
                                        <input type="text"
                                               id="date-field-{{ $rule->id }}"
                                               name="fields[{{ $rule->id }}]"
                                               {{ $rule->is_required ? 'required' : '' }}
                                               placeholder="Pilih tanggal..."
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white cursor-pointer">
                                    </div>

                                @elseif($rule->field_type === 'time')
                                    <input type="text"
                                           id="time-field-{{ $rule->id }}"
                                           name="fields[{{ $rule->id }}]"
                                           {{ $rule->is_required ? 'required' : '' }}
                                           placeholder="Pilih waktu..."
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white cursor-pointer">

                                @elseif($rule->field_type === 'datetime')
                                    <input type="text"
                                           id="datetime-field-{{ $rule->id }}"
                                           name="fields[{{ $rule->id }}]"
                                           {{ $rule->is_required ? 'required' : '' }}
                                           placeholder="Pilih tanggal & waktu..."
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white cursor-pointer">

                                @elseif($rule->field_type === 'file' || $rule->field_type === 'image')
                                    <div class="space-y-3">
                                        <input type="file" name="fields[{{ $rule->id }}]"
                                               id="file-field-{{ $rule->id }}"
                                               {{ $rule->is_required ? 'required' : '' }}
                                               accept="{{ $rule->field_type === 'image' ? 'image/*' : '*' }}"
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-900 dark:file:text-amber-300 dark:hover:file:bg-amber-800"
                                               onchange="previewFile(this, {{ $rule->id }}, '{{ $rule->field_type }}')">

                                        <!-- Preview Container -->
                                        <div id="preview-{{ $rule->id }}" class="hidden">
                                            <div class="relative">
                                                <img id="preview-image-{{ $rule->id }}"
                                                     src=""
                                                     alt="Preview"
                                                     class="max-w-full max-h-64 rounded-lg border border-gray-300 dark:border-gray-600 shadow-md">
                                                <button type="button"
                                                        onclick="clearFile({{ $rule->id }}, '{{ $rule->field_type }}')"
                                                        class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <p id="preview-filename-{{ $rule->id }}" class="mt-2 text-sm text-gray-600 dark:text-gray-400 truncate"></p>
                                        </div>
                                    </div>

                                @elseif($rule->field_type === 'select')
                                    <select name="fields[{{ $rule->id }}]"
                                            {{ $rule->is_required ? 'required' : '' }}
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                        <option value="">Select an option</option>
                                        {{-- Options would come from rule options --}}
                                    </select>

                                @elseif($rule->field_type === 'radio')
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="fields[{{ $rule->id }}]" value="1"
                                                   {{ $rule->is_required ? 'required' : '' }}
                                                   class="mr-2 text-amber-500 focus:ring-amber-500">
                                            <span class="text-gray-700 dark:text-gray-300">Option 1</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="fields[{{ $rule->id }}]" value="2"
                                                   {{ $rule->is_required ? 'required' : '' }}
                                           class="mr-2 text-amber-500 focus:ring-amber-500">
                                            <span class="text-gray-700 dark:text-gray-300">Option 2</span>
                                        </label>
                                    </div>

                                @elseif($rule->field_type === 'checkbox')
                                    <label class="flex items-center">
                                        <input type="checkbox" name="fields[{{ $rule->id }}]" value="1"
                                               class="mr-2 text-amber-500 focus:ring-amber-500">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $rule->label }}</span>
                                    </label>

                                @elseif($rule->field_type === 'toggle')
                                    <label class="flex items-center">
                                        <input type="checkbox" name="fields[{{ $rule->id }}]" value="1"
                                               class="mr-2 text-amber-500 focus:ring-amber-500">
                                        <span class="text-gray-700 dark:text-gray-300">Toggle On/Off</span>
                                    </label>
                                @endif
                            </div>
                        @endforeach

                        <div class="flex items-center justify-end">
                            <button type="submit"
                                    class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Submit Progress
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-6">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">⚠️</span>
                            <div>
                                <h3 class="font-semibold text-amber-900 dark:text-amber-300">No Form Fields Available</h3>
                                <p class="text-amber-800 dark:text-amber-400 text-sm mt-1">
                                    This challenge doesn't have any form fields configured yet.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tab Content: History Progress -->
            <div id="content-history-progress" class="tab-content p-6 md:p-8 hidden">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">History Progress</h3>
                    @if(auth()->check())
                        <span class="text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                            Showing your submissions only
                        </span>
                    @endif
                </div>

                <!-- Progress Overview Cards -->
                @if($userSubmissions->count() > 0)
                    @php
                        $totalDays = $challenge->duration_days;
                        $submittedDays = $userSubmissions->count();
                        $progressPercentage = min(100, round(($submittedDays / $totalDays) * 100));

                        // Calculate current streak
                        $currentStreak = 0;
                        $submissionDates = $userSubmissions
                            ->pluck('day_number')
                            ->map(function($timestamp) {
                                return \Carbon\Carbon::createFromTimestamp($timestamp)->startOfDay();
                            })
                            ->sortByDesc(function($date) {
                                return $date->timestamp;
                            })
                            ->values();

                        if ($submissionDates->isNotEmpty()) {
                            $mostRecent = $submissionDates->first();
                            $today = now()->startOfDay();
                            $daysDiff = $today->diffInDays($mostRecent);

                            if ($daysDiff <= 1) {
                                $currentStreak = 1;
                                $previousDate = $mostRecent;

                                foreach ($submissionDates->skip(1) as $date) {
                                    $diffDays = $previousDate->diffInDays($date);
                                    if ($diffDays === 1) {
                                        $currentStreak++;
                                        $previousDate = $date;
                                    } else {
                                        break;
                                    }
                                }
                            }
                        }

                        // Calculate longest streak
                        $longestStreak = 0;
                        if ($submissionDates->isNotEmpty()) {
                            $streak = 1;
                            for ($i = 0; $i < $submissionDates->count() - 1; $i++) {
                                $diffDays = $submissionDates[$i]->diffInDays($submissionDates[$i + 1]);
                                if ($diffDays === 1) {
                                    $streak++;
                                } else {
                                    $longestStreak = max($longestStreak, $streak);
                                    $streak = 1;
                                }
                            }
                            $longestStreak = max($longestStreak, $streak);
                        }
                    @endphp

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <!-- Progress Bar Card -->
                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Progress
                                </h4>
                                <span class="text-2xl font-bold text-amber-600">{{ $progressPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-2">
                                <div class="bg-gradient-to-r from-amber-500 to-orange-600 h-3 rounded-full transition-all duration-500 ease-out"
                                     style="width: {{ $progressPercentage }}%">
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $submittedDays }} of {{ $totalDays }} days completed
                            </p>
                        </div>

                        <!-- Current Streak Card -->
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                                    </svg>
                                    Current Streak
                                </h4>
                                <span class="text-2xl font-bold text-purple-600 flex items-center gap-1">
                                    {{ $currentStreak }}
                                    <span class="text-lg">🔥</span>
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                @for($i = 0; $i < min(7, $currentStreak); $i++)
                                    <div class="w-3 h-3 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600"></div>
                                @endfor
                                @if($currentStreak < 7)
                                    @for($i = $currentStreak; $i < 7; $i++)
                                        <div class="w-3 h-3 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                    @endfor
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                {{ $currentStreak >= 7 ? 'Amazing! You\'re on fire!' : ($currentStreak >= 3 ? 'Great job! Keep going!' : 'Start your streak today!') }}
                            </p>
                        </div>

                        <!-- Longest Streak Card -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                    Best Streak
                                </h4>
                                <span class="text-2xl font-bold text-green-600 flex items-center gap-1">
                                    {{ $longestStreak }}
                                    <span class="text-lg">🏆</span>
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                @for($i = 0; $i < min(7, $longestStreak); $i++)
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @endfor
                                @if($longestStreak < 7)
                                    @for($i = $longestStreak; $i < 7; $i++)
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300 dark:border-gray-600"></div>
                                    @endfor
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Your personal record!
                            </p>
                        </div>
                    </div>
                @endif

                @if($userSubmissions->count() > 0)
                    <div class="space-y-4">
                        @foreach($userSubmissions as $submission)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center flex-1">
                                        <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $submission->formatted_date }}
                                                </h4>
                                                @if($submission->user && $submission->user->id === auth()->id())
                                                    <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 px-2 py-0.5 rounded-full">
                                                        You
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Submitted: {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y - H:i') : 'Not submitted yet' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @php
                                            $statusClasses = match($submission->status) {
                                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                            };

                                            // Get first image from submission values for sharing
                                            $firstImage = null;
                                            if($submission->values && $submission->values->count() > 0) {
                                                foreach($submission->values as $value) {
                                                    if($value->rule && ($value->rule->field_type === 'image' || $value->rule->field_type === 'file')) {
                                                        if($value->value_text) {
                                                            $filePath = $value->value_text;
                                                            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                                            if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'])) {
                                                                $firstImage = asset('storage/' . $value->value_text);
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            // Build share text
                                            $shareText = "🎯 " . $challenge->title . "\n\n";
                                            $shareText .= "📅 " . $submission->formatted_date . "\n";
                                            $shareText .= "📊 Progress: " . $submittedDays . "/" . $totalDays . " days (" . $progressPercentage . "%)\n";
                                            $shareText .= "🔥 Current Streak: " . $currentStreak . " days\n";
                                            $shareText .= "🏆 Best Streak: " . $longestStreak . " days\n\n";
                                            $shareText .= "✨ Status: " . ucfirst($submission->status) . "\n\n";
                                            $shareText .= "Join the challenge! 💪";
                                            $shareTextEncoded = urlencode($shareText);
                                            $shareUrl = url()->current();
                                        @endphp

                                        <span class="px-3 py-1 text-xs font-semibold rounded {{ $statusClasses }}">
                                            {{ ucfirst($submission->status) }}
                                        </span>

                                        @if($firstImage)
                                            <a href="{{ route('challenges.submissions.show', ['slug' => $challenge->slug, 'submission' => $submission->id]) }}"
                                               class="flex items-center gap-1 px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded transition shadow-sm"
                                               title="Share submission"
                                               target="_blank">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                                </svg>
                                                <span>Share</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                @if($submission->values && $submission->values->count() > 0)
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mt-3">
                                        <dl class="grid gap-2">
                                            @foreach($submission->values as $value)
                                                @if($value->rule)
                                                    <div class="flex flex-col sm:flex-row sm:justify-between gap-2 py-2 border-b border-gray-200 dark:border-gray-600 last:border-0">
                                                        <dt class="text-sm text-gray-600 dark:text-gray-400">
                                                            {{ $value->rule->label }}
                                                            <span class="text-xs text-gray-400 ml-1">({{ $value->rule->field_type }})</span>
                                                        </dt>
                                                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                                            @if($value->rule->field_type === 'image')
                                                                @if($value->value_text)
                                                                    <div class="relative group">
                                                                        <img src="{{ asset('storage/' . $value->value_text) }}"
                                                                             alt="{{ $value->rule->label }}"
                                                                             class="max-w-full md:max-w-md max-h-64 w-auto rounded-lg object-cover border border-gray-200 dark:border-gray-600 cursor-pointer transition-transform duration-300 group-hover:scale-105 shadow-lg"
                                                                             onclick="openImageModal('{{ asset('storage/' . $value->value_text) }}')"
                                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300 rounded-lg flex items-center justify-center pointer-events-none">
                                                                            <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                                    <span class="text-red-500 hidden" style="display:none">Image not found</span>
                                                                @else
                                                                    <span class="text-gray-400 italic">No image uploaded</span>
                                                                @endif

                                                            @elseif($value->rule->field_type === 'file')
                                                                @if($value->value_text)
                                                                    @php
                                                                        $filePath = $value->value_text;
                                                                        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
                                                                    @endphp
                                                                    @if($isImage)
                                                                        <div class="relative group">
                                                                            <img src="{{ asset('storage/' . $value->value_text) }}"
                                                                                 alt="{{ $value->rule->label }}"
                                                                                 class="max-w-full md:max-w-md max-h-64 w-auto rounded-lg object-cover border border-gray-200 dark:border-gray-600 cursor-pointer transition-transform duration-300 group-hover:scale-105 shadow-lg"
                                                                                 onclick="openImageModal('{{ asset('storage/' . $value->value_text) }}')"
                                                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300 rounded-lg flex items-center justify-center pointer-events-none">
                                                                                <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                                                </svg>
                                                                            </div>
                                                                        </div>
                                                                        <span class="text-red-500 hidden" style="display:none">Image not found</span>
                                                                    @else
                                                                        <a href="{{ asset('storage/' . $value->value_text) }}"
                                                                           target="_blank"
                                                                           class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 flex items-center">
                                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                            </svg>
                                                                            Download File
                                                                        </a>
                                                                    @endif
                                                                @else
                                                                    <span class="text-gray-400 italic">No file uploaded</span>
                                                                @endif

                                                            @elseif($value->rule->field_type === 'textarea' || $value->rule->field_type === 'text')
                                                                <div class="whitespace-pre-wrap">{{ $value->value_text ?? '-' }}</div>

                                                            @elseif($value->rule->field_type === 'number')
                                                                {{ $value->value_number ?? $value->value_text ?? '-' }}

                                                            @elseif($value->rule->field_type === 'checkbox' || $value->rule->field_type === 'toggle')
                                                                {{ $value->value_boolean !== null ? ($value->value_boolean ? '✓ Yes' : '✗ No') : '-' }}

                                                            @elseif($value->rule->field_type === 'radio' || $value->rule->field_type === 'select')
                                                                {{ $value->value_text ?? '-' }}

                                                            @else
                                                                {{-- Fallback for other types --}}
                                                                @if($value->value_text)
                                                                    {{ $value->value_text }}
                                                                @elseif($value->value_number !== null)
                                                                    {{ $value->value_number }}
                                                                @elseif($value->value_boolean !== null)
                                                                    {{ $value->value_boolean ? '✓ Yes' : '✗ No' }}
                                                                @else
                                                                    <span class="text-gray-400 italic">No value</span>
                                                                @endif
                                                            @endif
                                                        </dd>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </dl>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-8 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Progress Yet</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            You haven't submitted any progress yet. Start by filling out the form above!
                        </p>
                    </div>
                @endif
            </div>

            <!-- Tab Content: Board Top 10 -->
            <div id="content-top-10" class="tab-content p-6 md:p-8 hidden">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">🏆 Top 10 Participants</h3>

                @if($topParticipants->count() > 0)
                    <div class="space-y-3">
                        @foreach($topParticipants as $index => $participant)
                            <div class="flex items-center justify-between p-4 rounded-lg
                                {{ $index === 0 ? 'bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-400' :
                                   ($index === 1 ? 'bg-gray-100 dark:bg-gray-700 border-2 border-gray-300' :
                                   ($index === 2 ? 'bg-orange-50 dark:bg-orange-900/20 border-2 border-orange-400' :
                                   'bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600')) }}">
                                <div class="flex items-center flex-1">
                                    <span class="text-2xl font-bold mr-4
                                        {{ $index === 0 ? 'text-yellow-600' :
                                           ($index === 1 ? 'text-gray-500' :
                                           ($index === 2 ? 'text-orange-600' : 'text-gray-400')) }}">
                                        #{{ $index + 1 }}
                                    </span>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">
                                            {{ $participant->user->name ?? 'Anonymous' }}
                                        </h4>
                                        <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                            <span>{{ $participant->submissions_count }} submissions</span>
                                            @if(isset($participant->streak) && $participant->streak > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    {{ $participant->streak >= 7 ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' :
                                                       ($participant->streak >= 3 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                                       'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300') }}">
                                                    🔥 {{ $participant->streak }} day streak
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($index === 0)
                                        <span class="text-3xl">🥇</span>
                                    @elseif($index === 1)
                                        <span class="text-3xl">🥈</span>
                                    @elseif($index === 2)
                                        <span class="text-3xl">🥉</span>
                                    @else
                                        <span class="text-2xl">⭐</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-8 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Participants Yet</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Be the first one to join this challenge and reach the top of the leaderboard!
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-4 mb-6">
        @if(auth()->check() && $challenge->status === 'active')
            @if(!$isParticipant)
                <form method="POST" action="{{ route('challenges.join', $challenge->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Join Challenge
                    </button>
                </form>
            @else
                <div class="flex flex-wrap gap-4">
                    <button disabled class="px-6 py-3 bg-green-500 text-white font-semibold rounded-lg flex items-center cursor-default opacity-75">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Joined
                    </button>
                    <form method="POST" action="{{ route('challenges.leave', $challenge->id) }}" class="inline"
                          onsubmit="return confirm('Are you sure you want to leave this challenge? Your progress will be lost.')">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Leave Challenge
                        </button>
                    </form>
                </div>
            @endif
        @endif

        @if(auth()->check() && $challenge->created_by === auth()->id())
            <a href="{{ route('filament.admin.resources.challenges.edit', $challenge->id) }}"
               class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Challenge
            </a>
        @endif
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 z-50 hidden" onclick="closeImageModal()">
    <div class="absolute inset-0 bg-black bg-opacity-90 backdrop-blur-sm"></div>
    <div class="relative h-full flex items-center justify-center p-4">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 p-2 bg-black bg-opacity-50 rounded-full">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" alt="Full size preview" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active state from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('border-amber-500', 'text-amber-600', 'dark:text-amber-400');
        button.classList.add('border-transparent', 'text-gray-600', 'hover:text-gray-800', 'dark:text-gray-400', 'dark:hover:text-gray-200');
    });

    // Show selected tab content
    const selectedContent = document.getElementById('content-' + tabName);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }

    // Add active state to selected tab button
    const selectedButton = document.getElementById('tab-' + tabName);
    if (selectedButton) {
        selectedButton.classList.remove('border-transparent', 'text-gray-600', 'hover:text-gray-800', 'dark:text-gray-400', 'dark:hover:text-gray-200');
        selectedButton.classList.add('border-amber-500', 'text-amber-600', 'dark:text-amber-400');
    }
}

function openImageModal(imageSrc) {
    event.stopPropagation();
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageSrc;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});

// File Preview Function
function previewFile(input, fieldId, fieldType) {
    const previewContainer = document.getElementById('preview-' + fieldId);
    const previewImage = document.getElementById('preview-image-' + fieldId);
    const previewFilename = document.getElementById('preview-filename-' + fieldId);

    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Show filename
        if (previewFilename) {
            previewFilename.textContent = 'Selected: ' + file.name;
        }

        // For image files, show preview
        if (fieldType === 'image' || file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }

            reader.readAsDataURL(file);
        } else {
            // For non-image files, just show filename
            previewImage.classList.add('hidden');
            previewContainer.classList.remove('hidden');
        }
    } else {
        clearFile(fieldId, fieldType);
    }
}

// Clear File Function
function clearFile(fieldId, fieldType) {
    const input = document.getElementById('file-field-' + fieldId);
    const previewContainer = document.getElementById('preview-' + fieldId);
    const previewImage = document.getElementById('preview-image-' + fieldId);
    const previewFilename = document.getElementById('preview-filename-' + fieldId);

    // Clear the input
    input.value = '';

    // Hide preview
    previewContainer.classList.add('hidden');

    // Clear image source
    previewImage.src = '';

    // Clear filename
    if (previewFilename) {
        previewFilename.textContent = '';
    }
}

// Initialize datepickers when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr for submission date
    const submissionDatepicker = document.getElementById('submission-datepicker');
    if (submissionDatepicker) {
        flatpickr("#submission-datepicker", {
            dateFormat: "Y-m-d",
            minDate: "{{ $challenge->start_date->format('Y-m-d') }}",
            maxDate: "{{ $challenge->end_date->format('Y-m-d') }}",
            allowInput: true,
            disableMobile: true,
            theme: "amber",
            locale: {
                firstDayOfWeek: 1 // Monday as first day
            }
        });
    }

    // Initialize Flatpickr for date inputs
    const dateInputs = document.querySelectorAll('input[id^="date-field-"]');

    dateInputs.forEach(function(input) {
        flatpickr("#" + input.id, {
            dateFormat: "Y-m-d",
            allowInput: true,
            disableMobile: true,
            theme: "amber",
            locale: {
                firstDayOfWeek: 1 // Monday as first day
            }
        });
    });

    // Initialize Flatpickr for datetime inputs
    const datetimeInputs = document.querySelectorAll('input[id^="datetime-field-"]');

    datetimeInputs.forEach(function(input) {
        flatpickr("#" + input.id, {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            allowInput: true,
            disableMobile: true,
            theme: "amber",
            time_24hr: true
        });
    });

    // Initialize Flatpickr for time inputs
    const timeInputs = document.querySelectorAll('input[id^="time-field-"]');

    timeInputs.forEach(function(input) {
        flatpickr("#" + input.id, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            allowInput: true,
            disableMobile: true,
            theme: "amber",
            time_24hr: true
        });
    });
});

// Share challenge functions
function shareChallengeToWhatsApp() {
    const shareText = `{{ $challenge->title }}\n\n` +
                     `Duration: {{ $challenge->duration_days }} days\n` +
                     `Start: {{ $challenge->start_date->format('M d, Y') }}\n` +
                     `End: {{ $challenge->end_date->format('M d, Y') }}\n` +
                     `Participants: {{ $participantsCount ?? $challenge->participants_count ?? 0 }}\n\n` +
                     `Join this challenge and achieve your goals together!\n\n` +
                     `{{ request()->fullUrl() }}`;

    const whatsappUrl = 'https://wa.me/?text=' + encodeURIComponent(shareText);
    window.open(whatsappUrl, '_blank');
}

function copyChallengeLink() {
    const link = '{{ request()->fullUrl() }}';

    if (navigator.clipboard) {
        navigator.clipboard.writeText(link).then(function() {
            alert('Link copied to clipboard!');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = link;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Link copied to clipboard!');
    }
}

function shareChallengeToFacebook() {
    const url = '{{ request()->fullUrl() }}';
    const facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
    window.open(facebookUrl, '_blank', 'width=600,height=400');
}

function shareChallengeToTwitter() {
    const text = `Join "{{ $challenge->title }}" - A {{ $challenge->duration_days }}-day challenge! Start your journey today!`;
    const url = '{{ request()->fullUrl() }}';
    const twitterUrl = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(text) + '&url=' + encodeURIComponent(url);
    window.open(twitterUrl, '_blank', 'width=600,height=400');
}
</script>
@endsection
