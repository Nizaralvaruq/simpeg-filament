<?php

declare(strict_types=1);

namespace Modules\Presensi\Policies;

use App\Models\User;
use Modules\Presensi\Models\Absensi;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsensiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('Absensi:viewAny');
    }

    public function view(User $user, Absensi $absensi): bool
    {
        return $user->can('Absensi:view');
    }

    public function create(User $user): bool
    {
        return $user->can('Absensi:create');
    }

    public function update(User $user, Absensi $absensi): bool
    {
        // Staff can only update their own attendance
        if ($user->hasRole('staff')) {
            return $user->can('Absensi:update') && $absensi->user_id === $user->id;
        }

        return $user->hasRole('super_admin') || $user->can('Absensi:update');
    }

    public function delete(User $user, Absensi $absensi): bool
    {
        // Staff can only delete their own attendance
        if ($user->hasRole('staff')) {
            return $user->can('Absensi:delete') && $absensi->user_id === $user->id;
        }

        return $user->hasRole('super_admin') || $user->can('Absensi:delete');
    }

    public function restore(User $user, Absensi $absensi): bool
    {
        return $user->can('Absensi:restore') || $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Absensi $absensi): bool
    {
        return $user->can('Absensi:forceDelete') || $user->hasRole('super_admin');
    }
}
