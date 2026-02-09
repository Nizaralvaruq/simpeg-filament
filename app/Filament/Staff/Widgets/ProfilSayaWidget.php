<?php

namespace App\Filament\Staff\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Modules\Kepegawaian\Models\DataInduk;

class ProfilSayaWidget extends Widget
{
    protected static ?int $sort = 103;
    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && $user->can('View:ProfilSayaWidget');
    }
    protected string $view = 'filament.staff.widgets.staff-profile-widget';

    protected int | string | array $columnSpan = 'full';

    public function getEmployee()
    {
        return DataInduk::where('user_id', Auth::id())->first();
    }
}
