<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Participant Info -->
        <x-filament::section>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Participant Information
                    </h3>
                    <div class="mt-3 space-y-2">
                        <p class="text-sm">
                            <span class="font-medium">Name:</span>
                            {{ $participant->user->name ?? 'N/A' }}
                        </p>
                        <p class="text-sm">
                            <span class="font-medium">Email:</span>
                            {{ $participant->user->email ?? 'N/A' }}
                        </p>
                        <p class="text-sm">
                            <span class="font-medium">Joined:</span>
                            {{ $participant->created_at->format('M d, Y H:i') }}
                        </p>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Submission Statistics
                    </h3>
                    <div class="mt-3 grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $totalSubmissions }}
                            </p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                            <p class="text-xs text-green-600 dark:text-green-400">Approved</p>
                            <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                                {{ $approvedSubmissions }}
                            </p>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg">
                            <p class="text-xs text-yellow-600 dark:text-yellow-400">Pending</p>
                            <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">
                                {{ $pendingSubmissions }}
                            </p>
                        </div>
                        <div class="bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
                            <p class="text-xs text-red-600 dark:text-red-400">Rejected</p>
                            <p class="text-2xl font-bold text-red-700 dark:text-red-300">
                                {{ $rejectedSubmissions }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Submission Details List -->
        @if($participant->submissions && $participant->submissions->count() > 0)
            @foreach($participant->submissions as $submission)
                <x-filament::section>
                    <div class="space-y-4">
                        <!-- Submission Header -->
                        <div class="flex items-center justify-between border-b pb-3">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    Submission #{{ $loop->iteration }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $submission->formatted_date }} (Day {{ $loop->iteration }})
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($submission->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($submission->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                    @elseif($submission->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                    @endif">
                                    {{ ucfirst($submission->status) }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $submission->created_at->format('M d, Y H:i') }}
                                </span>
                            </div>
                        </div>

                        <!-- Submission Data -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @if($submission->values && $submission->values->count() > 0)
                                @foreach($submission->values as $value)
                                    @if($value->rule)
                                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-lg space-y-3 shadow-sm">
                                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                                                {{ $value->rule->label }}
                                            </label>

                                            <!-- Text/Textarea/Select Fields -->
                                            @if(in_array($value->rule->field_type, ['text', 'textarea', 'select', 'radio']))
                                                <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-md">
                                                    <p class="text-sm text-gray-900 dark:text-gray-100 break-words leading-relaxed">
                                                        {{ $value->value_text ?: '-' }}
                                                    </p>
                                                </div>
                                            @endif

                                            <!-- Number Field -->
                                            @if($value->rule->field_type === 'number')
                                                <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-md">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ $value->value_number ?? '-' }}
                                                    </p>
                                                </div>
                                            @endif

                                            <!-- Date/Time Fields -->
                                            @if(in_array($value->rule->field_type, ['date', 'time', 'datetime']))
                                                <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-md">
                                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                                        {{ $value->value_text ?: '-' }}
                                                    </p>
                                                </div>
                                            @endif

                                            <!-- Checkbox/Toggle -->
                                            @if(in_array($value->rule->field_type, ['checkbox', 'toggle']))
                                                <div class="flex items-center bg-gray-50 dark:bg-gray-900/50 p-3 rounded-md">
                                                    @if($value->value_boolean)
                                                        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="ml-2 text-sm font-medium text-green-600 dark:text-green-400">Yes</span>
                                                    @else
                                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">No</span>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- File Upload -->
                                            @if($value->rule->field_type === 'file')
                                                @if($value->file_url)
                                                    <a href="{{ $value->file_url }}" target="_blank"
                                                       class="inline-flex items-center px-3 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-all duration-200 border border-blue-200 dark:border-blue-800">
                                                        <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        View File
                                                    </a>
                                                @else
                                                    <p class="text-sm text-gray-400 dark:text-gray-500 italic">No file uploaded</p>
                                                @endif
                                            @endif

                                            <!-- Image Upload -->
                                            @if($value->rule->field_type === 'image')
                                                @if($value->file_url)
                                                    <a href="{{ $value->file_url }}" target="_blank"
                                                       class="block relative group">
                                                        <img src="{{ $value->file_url }}"
                                                             alt="{{ $value->rule->label }}"
                                                             class="w-full h-24 object-cover rounded-md border border-gray-300 dark:border-gray-600 group-hover:border-blue-500 transition-all duration-200 shadow-sm">
                                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-md flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                            </svg>
                                                        </div>
                                                    </a>
                                                @else
                                                    <p class="text-sm text-gray-400 dark:text-gray-500 italic">No image uploaded</p>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="col-span-full">
                                    <div class="bg-gray-50 dark:bg-gray-800/50 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No submission data available</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Submission Actions -->
                        <div class="flex items-center justify-end gap-2 pt-3 border-t">
                            <a href="{{ route('filament.admin.resources.submissions.view', ['record' => $submission]) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Full Details
                            </a>
                        </div>
                    </div>
                </x-filament::section>
            @endforeach
        @else
            <x-filament::section>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No submissions yet</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This participant hasn't submitted any data yet.</p>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
