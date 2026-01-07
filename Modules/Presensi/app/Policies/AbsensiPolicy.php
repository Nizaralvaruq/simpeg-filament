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
        return $user->can('ViewAny:Absensi');
    }

    public function view(User $user, Absensi $absensi): bool
    {
        return $user->can('View:Absensi');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Absensi');
    }

    public function update(User $user, Absensi $absensi): bool
    {
        if ($user->hasRole('super_admin')) return true;

        // Managers can update any record in their scope
        if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah', 'ketua_psdm'])) {
            return $user->can('Update:Absensi');
        }

        // Staff can only update their own attendance
        if ($user->hasRole('staff')) {
            return $user->can('Update:Absensi') && $absensi->user_id === $user->id;
        }

        return $user->can('Update:Absensi');
    }

    public function delete(User $user, Absensi $absensi): bool
    {
        if ($user->hasRole('super_admin')) return true;

        // Managers can delete any record in their scope
        if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah', 'ketua_psdm'])) {
            return $user->can('Delete:Absensi');
        }

        // Staff can only delete their own attendance
        if ($user->hasRole('staff')) {
            return $user->can('Delete:Absensi') && $absensi->user_id === $user->id;
        }

        return $user->can('Delete:Absensi');
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
