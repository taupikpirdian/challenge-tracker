<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class DashboardGreetingWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-greeting-widget';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::check();
    }
}
