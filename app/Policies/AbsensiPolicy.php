<?php

namespace App\Policies;

use App\Models\User;
use Modules\Presensi\Models\Absensi;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsensiPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('Absensi:viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Absensi $absensi): bool
    {
        return $user->can('Absensi:view');
    }

    /**
     * Determine whether the user can create models.
     * Only staff can create attendance records.
     */
    public function create(User $user): bool
    {
        return $user->can('Absensi:create');
    }

    /**
     * Determine whether the user can update the model.
     * Only staff can update their own attendance.
     */
    public function update(User $user, Absensi $absensi): bool
    {
        // Staff can only update their own attendance
        if ($user->hasRole('staff')) {
            return $user->can('Absensi:update') && $absensi->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * Only staff can delete their own attendance.
     */
    public function delete(User $user, Absensi $absensi): bool
    {
        // Staff can only delete their own attendance
        if ($user->hasRole('staff')) {
            return $user->can('Absensi:delete') && $absensi->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Absensi $absensi): bool
    {
        return $user->can('Absensi:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Absensi $absensi): bool
    {
        return $user->can('Absensi:forceDelete');
    }
}
