<?php

namespace App\Filament\Staff\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Modules\Kepegawaian\Models\DataInduk;

class StaffProfileWidget extends Widget
{
    protected string $view = 'filament.staff.widgets.staff-profile-widget';

    protected int | string | array $columnSpan = 'full';

    public function getEmployee()
    {
        return DataInduk::where('user_id', Auth::id())->first();
    }
}
