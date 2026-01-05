@props([
    'submission' => null,
])

@if(!$submission || !$submission->values || $submission->values->isEmpty())
    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
        <p>No submission data available.</p>
    </div>
@else
    <div class="space-y-0">
        @foreach($submission->values as $value)
            @if($value->rule)
                <div class="flex flex-col sm:flex-row sm:justify-between gap-2 py-3 border-b border-gray-200 dark:border-gray-600 last:border-0">
                    <dt class="text-sm text-gray-600 dark:text-gray-400 min-w-[150px]">
                        {{ $value->rule->label }}
                        <span class="text-xs text-gray-400">({{ $value->rule->field_type }})</span>
                    </dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                        @if($value->rule->field_type === 'image')
                            @if($value->value_text)
                                <a href="{{ \App\Helpers\MinioHelper::getProxyUrl($value->value_text) }}" target="_blank"
                                   class="inline-block">
                                    <img src="{{ \App\Helpers\MinioHelper::getProxyUrl($value->value_text) }}"
                                         alt="{{ $value->rule->label }}"
                                         class="max-w-xs max-h-48 rounded-lg border border-gray-300 dark:border-gray-600 hover:opacity-90 transition cursor-pointer object-cover">
                                </a>
                            @else
                                <span class="text-gray-400 italic">No image uploaded</span>
                            @endif

                        @elseif($value->rule->field_type === 'file')
                            @if($value->value_text)
                                <a href="{{ \App\Helpers\MinioHelper::getProxyUrl($value->value_text) }}"
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 inline-flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Download File ({{ basename($value->value_text) }})
                                </a>
                            @else
                                <span class="text-gray-400 italic">No file uploaded</span>
                            @endif

                        @elseif($value->rule->field_type === 'checkbox' || $value->rule->field_type === 'toggle')
                            @if($value->value_boolean !== null)
                                @if($value->value_boolean)
                                    <span class="text-green-600 dark:text-green-400 font-semibold">✓ Yes</span>
                                @else
                                    <span class="text-red-600 dark:text-red-400 font-semibold">✗ No</span>
                                @endif
                            @else
                                <span>-</span>
                            @endif

                        @elseif($value->rule->field_type === 'textarea' || $value->rule->field_type === 'text')
                            <div class="whitespace-pre-wrap bg-gray-50 dark:bg-gray-700 p-3 rounded text-sm">
                                {{ $value->value_text ?? '-' }}
                            </div>

                        @elseif($value->rule->field_type === 'number')
                            <span class="font-mono font-semibold">
                                {{ $value->value_number ?? $value->value_text ?? '-' }}
                            </span>

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
    </div>
@endif
