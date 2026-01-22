<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    /**
     * @var string|view
     */
    protected string $view = 'filament.pages.auth.login';

    public function getHeading(): string | Htmlable
    {
        return '';
    }
}
