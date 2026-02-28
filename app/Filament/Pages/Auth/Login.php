<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
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

    /**
     * Map internal role names to human-friendly Indonesian labels.
     */
    protected function getRoleLabel(string $role): string
    {
        return match ($role) {
            'super_admin'   => 'Super Admin',
            'ketua_psdm'    => 'Ketua PSDM',
            'koor_jenjang'  => 'Koordinator Jenjang',
            'kepala_sekolah' => 'Kepala Sekolah',
            'admin_unit'    => 'Admin Unit',
            'staff'         => 'Staff',
            default         => ucwords(str_replace('_', ' ', $role)),
        };
    }

    /**
     * Override authenticate to send a role-info notification on successful login.
     */
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        // If login was successful, the parent returns a LoginResponse object
        if ($response !== null) {
            /** @var \App\Models\User $user */
            $user = Filament::auth()->user();

            if ($user) {
                // Get all roles the user has
                $roles = $user->getRoleNames()->map(fn($role) => $this->getRoleLabel($role));

                $roleLabel = $roles->isNotEmpty()
                    ? $roles->implode(', ')
                    : 'Pengguna';

                $employeeName = optional($user->employee)->nama_lengkap ?? $user->name;

                Notification::make()
                    ->title("Selamat datang, {$employeeName}!")
                    ->body("Anda masuk sebagai: **{$roleLabel}**")
                    ->success()
                    ->seconds(6)
                    ->send();
            }
        }

        return $response;
    }
}
