<x-filament-panels::page class="fi-dashboard-page">
    {{-- Custom Dashboard Greeting --}}
    <div class="fi-custom-dashboard-heading px-6 pt-6 pb-0">
        <div class="fi-custom-dashboard-greeting rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Selamat datang di Challenge Dashboard!
            </h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Senang melihatmu kembali. Dashboard ini membantumu mengikuti challenge harian dengan lebih teratur dan konsisten.
            </p>
        </div>
    </div>

    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                ...$this->getWidgetData(),
            ]
        "
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>
