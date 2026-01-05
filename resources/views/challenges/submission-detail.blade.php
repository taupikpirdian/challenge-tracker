@extends('layouts.app')

@php
    // Generate dynamic metadata based on submission data
    $metaTitle = $submission->user->name . "'s Progress - " . $challenge->title . ' | Challenge Tracker';

    // Simple meta description for SEO (minimal emoji)
    $metaDescription = $submission->user->name . "'s progress in " . $challenge->title . ". ";
    $metaDescription .= "Submitted on " . $submission->formatted_date . ". ";
    $metaDescription .= "Progress: " . $submittedDays . "/" . $totalDays . " days (" . $progressPercentage . "%). ";
    $metaDescription .= "Current Streak: " . $currentStreak . " days. ";
    $metaDescription .= "Status: " . ucfirst($submission->status) . ".";
    $metaDescription = \Illuminate\Support\Str::limit($metaDescription, 160);

    // Generate keywords
    $metaKeywords = collect([
        $challenge->title,
        'progress',
        $submission->formatted_date,
        $submission->user->name,
        'challenge tracker',
        'fitness challenge',
        $submission->status,
    ])->filter()->unique()->implode(', ');

    // OG description with basic emoji support (WhatsApp compatible)
    $ogDescription = $challenge->title . "\n\n";
    $ogDescription .= "Date: " . $submission->formatted_date . "\n";
    $ogDescription .= "Progress: " . $submittedDays . "/" . $totalDays . " days (" . $progressPercentage . "%)\n";
    $ogDescription .= "Current Streak: " . $currentStreak . " days\n";
    $ogDescription .= "Status: " . ucfirst($submission->status) . "\n\n";
    $ogDescription .= "By: " . $submission->user->name;
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
    {{ $submission->user->name ?? 'Challenge Tracker' }}
@endsection

@section('og_title')
    {{ $submission->user->name }}'s Progress - {{ $challenge->title }}
@endsection

@section('og_description')
    {{ $ogDescription }}
@endsection

@section('og_image')
    {{ $firstImage ?? $challenge->cover_image_url }}
@endsection

@section('og_url')
    {{ url()->current() }}
@endsection

@section('twitter_title')
    {{ $submission->user->name }}'s Progress - {{ $challenge->title }}
@endsection

@section('twitter_description')
    {{ $ogDescription }}
@endsection

@section('twitter_image')
    {{ $firstImage ?? $challenge->cover_image_url }}
@endsection

@section('twitter_url')
    {{ url()->current() }}
@endsection

@section('canonical')
    {{ url()->current() }}
@endsection

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header Card -->
    <div class="bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 rounded-xl shadow-lg p-6 md:p-8 mb-6 border border-amber-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                    {{ strtoupper(substr($submission->user->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $submission->user->name ?? 'Anonymous' }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Challenge Participant</p>
                </div>
            </div>
            @php
                $statusColors = match($submission->status) {
                    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 border-green-300 dark:border-green-700',
                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 border-red-300 dark:border-red-700',
                    default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 border-yellow-300 dark:border-yellow-700',
                };
            @endphp
            <span class="px-4 py-2 text-sm font-bold rounded-full border-2 {{ $statusColors }}">
                {{ ucfirst($submission->status) }}
            </span>
        </div>

        <div class="bg-white dark:bg-gray-700 rounded-xl p-4 shadow-md">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                {{ $challenge->title }}
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <div class="text-center">
                    <div class="text-2xl mb-1">📅</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Submission Date</div>
                    <div class="font-semibold text-gray-900 dark:text-white text-sm">
                        {{ $submission->formatted_date }}
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-1">📊</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Progress</div>
                    <div class="font-semibold text-gray-900 dark:text-white text-sm">
                        {{ $submittedDays }}/{{ $totalDays }} days
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-1">🔥</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Current Streak</div>
                    <div class="font-semibold text-gray-900 dark:text-white text-sm">
                        {{ $currentStreak }} days
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-1">⏱️</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Submitted</div>
                    <div class="font-semibold text-gray-900 dark:text-white text-sm">
                        {{ $submission->submitted_at ? $submission->submitted_at->diffForHumans() : 'Recently' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                Challenge Progress
            </span>
            <span class="text-sm font-bold text-amber-600 dark:text-amber-400">
                {{ $progressPercentage }}%
            </span>
        </div>
        <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 h-4 rounded-full transition-all duration-700 ease-out relative"
                 style="width: {{ $progressPercentage }}%">
                <div class="absolute inset-0 bg-white opacity-20 animate-pulse"></div>
            </div>
        </div>
        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 text-center">
            {{ $submittedDays }} of {{ $totalDays }} days completed
        </p>
    </div>

    <!-- Submission Details -->
    @if($submission->values && $submission->values->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Submission Details
                </h3>
            </div>

            <div class="p-6 space-y-6">
                @foreach($submission->values as $value)
                    @if($value->rule)
                        <div class="border-b border-gray-200 dark:border-gray-700 last:border-0 pb-4 last:pb-0">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block">
                                {{ $value->rule->label }}
                            </label>

                            @if($value->rule->field_type === 'image' && $value->value_text)
                                <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-lg">
                                    <img src="{{ asset('storage/' . $value->value_text) }}"
                                         alt="{{ $value->rule->label }}"
                                         class="w-full max-h-96 object-cover">
                                </div>

                            @elseif($value->rule->field_type === 'file' && $value->value_text)
                                @php
                                    $filePath = $value->value_text;
                                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
                                @endphp
                                @if($isImage)
                                    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-lg">
                                        <img src="{{ asset('storage/' . $value->value_text) }}"
                                             alt="{{ $value->rule->label }}"
                                             class="w-full max-h-96 object-cover">
                                    </div>
                                @else
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                        <a href="{{ asset('storage/' . $value->value_text) }}"
                                           target="_blank"
                                           class="inline-flex items-center text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download File
                                        </a>
                                    </div>
                                @endif

                            @elseif(in_array($value->rule->field_type, ['text', 'textarea', 'number', 'date', 'time', 'datetime', 'select', 'radio']))
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                    @if($value->rule->field_type === 'textarea')
                                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $value->value_text ?? '-' }}</p>
                                    @elseif($value->rule->field_type === 'number')
                                        <p class="text-gray-900 dark:text-white text-lg font-semibold">{{ $value->value_number ?? $value->value_text ?? '-' }}</p>
                                    @else
                                        <p class="text-gray-900 dark:text-white">{{ $value->value_text ?? '-' }}</p>
                                    @endif
                                </div>

                            @elseif(in_array($value->rule->field_type, ['checkbox', 'toggle']))
                                <div class="flex items-center gap-2">
                                    @if($value->value_boolean)
                                        <span class="inline-flex items-center text-green-600 font-semibold">
                                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Yes
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-red-600 font-semibold">
                                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            No
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Share Buttons -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Share this progress</h3>
        <div class="flex flex-wrap gap-3">
            <button onclick="shareToWhatsApp()" class="flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition shadow-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp
            </button>

            <button onclick="copyLink()" class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Copy Link
            </button>

            <a href="{{ route('challenges.show', $challenge->slug) }}" class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Challenge
            </a>
        </div>
    </div>
</div>

<script>
function shareToWhatsApp() {
    // WhatsApp-compatible text without special emoji
    const shareText = `{{ $challenge->title }}\n\n` +
                     `Date: {{ $submission->formatted_date }}\n` +
                     `Progress: {{ $submittedDays }}/{{ $totalDays }} days ({{ $progressPercentage }}%)\n` +
                     `Current Streak: {{ $currentStreak }} days\n` +
                     `Status: {{ ucfirst($submission->status) }}\n\n` +
                     `By: {{ $submission->user->name }}\n\n` +
                     `{{ request()->fullUrl() }}`;

    const whatsappUrl = 'https://wa.me/?text=' + encodeURIComponent(shareText);
    window.open(whatsappUrl, '_blank');
}

function copyLink() {
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
</script>
@endsection
