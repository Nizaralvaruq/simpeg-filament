<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Component;
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

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->minLength(8)
            ->helperText('Minimal 8 karakter')
            ->validationMessages([
                'min' => 'Password harus minimal 8 karakter.',
            ]);
    }
}
