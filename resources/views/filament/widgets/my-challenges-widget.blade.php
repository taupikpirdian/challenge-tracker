<div style="width: 100%; display: block; clear: both; margin-top: 1.5rem;" class="my-challenges-widget w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            🎯 Langkah Harianku
        </h2>
        @if($challenges->count() > 0)
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $challenges->count() }} Challenge Aktif
            </span>
        @endif
    </div>

    @if($challenges->count() > 0)
        <!-- Grid Layout - All Challenges in Rows -->
        <div class="space-y-6">
            @foreach($challenges as $challenge)
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-6 w-full">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Cover Image -->
                        <div class="w-full md:w-1/3 flex-shrink-0">
                            @if($challenge->cover_image)
                                <img src="{{ Storage::url($challenge->cover_image) }}"
                                     alt="{{ $challenge->title }}"
                                     class="w-full h-48 md:h-full object-cover rounded-lg shadow-md">
                            @else
                                <div class="w-full h-48 bg-amber-100 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                    <span class="text-6xl">🏆</span>
                                </div>
                            @endif
                        </div>

                        <!-- Challenge Info -->
                        <div class="w-full md:w-2/3 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $challenge->title }}
                                    </h3>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full flex-shrink-0 ml-2
                                        {{ $challenge->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                           ($challenge->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                           'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                        {{ ucfirst($challenge->status) }}
                                    </span>
                                </div>

                                <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
                                    {!! \Str::limit(strip_tags($challenge->description), 200) !!}
                                </p>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($challenge->start_date)->format('M d') }} -
                                            {{ \Carbon\Carbon::parse($challenge->end_date)->format('M d') }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $challenge->duration_days }} Hari</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('filament.admin.resources.challenges.edit', ['record' => $challenge->id]) }}"
                                   class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-colors">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="text-6xl mb-4">🎯</div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                Belum Mengikuti Challenge
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Mulai ikuti challenge untuk melacak progres harianmu!
            </p>
            <a href="{{ route('filament.admin.resources.challenges.index') }}"
               class="inline-block px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-colors">
                Jelajahi Challenge
            </a>
        </div>
    @endif
</div>
