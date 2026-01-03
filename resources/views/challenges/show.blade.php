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
                            <input type="date"
                                   name="submission_date"
                                   required
                                   min="{{ $challenge->start_date->format('Y-m-d') }}"
                                   max="{{ $challenge->end_date->format('Y-m-d') }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
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
                                    <input type="date" name="fields[{{ $rule->id }}]"
                                           {{ $rule->is_required ? 'required' : '' }}
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white">

                                @elseif($rule->field_type === 'time')
                                    <input type="time" name="fields[{{ $rule->id }}]"
                                           {{ $rule->is_required ? 'required' : '' }}
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white">

                                @elseif($rule->field_type === 'datetime')
                                    <input type="datetime-local" name="fields[{{ $rule->id }}]"
                                           {{ $rule->is_required ? 'required' : '' }}
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white">

                                @elseif($rule->field_type === 'file' || $rule->field_type === 'image')
                                    <input type="file" name="fields[{{ $rule->id }}]"
                                           {{ $rule->is_required ? 'required' : '' }}
                                           accept="{{ $rule->field_type === 'image' ? 'image/*' : '*' }}"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white">

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
                                    @php
                                        $statusClasses = match($submission->status) {
                                            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-semibold rounded {{ $statusClasses }}">
                                        {{ ucfirst($submission->status) }}
                                    </span>
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
    <div class="flex flex-wrap gap-4">
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
</script>
@endsection
