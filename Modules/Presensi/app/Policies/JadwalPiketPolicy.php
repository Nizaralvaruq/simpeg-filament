<?php

declare(strict_types=1);

namespace Modules\Presensi\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Presensi\Models\JadwalPiket;
use Illuminate\Auth\Access\HandlesAuthorization;

class JadwalPiketPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JadwalPiket');
    }

    public function view(AuthUser $authUser, JadwalPiket $jadwalPiket): bool
    {
        if (!$authUser->can('View:JadwalPiket')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        if ($authUser->hasRole('staff')) {
            return $jadwalPiket->user_id === $authUser->id;
        }

        // For Unit Admins: Check if target employee belongs to my unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $jadwalPiket->user?->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JadwalPiket');
    }

    public function update(AuthUser $authUser, JadwalPiket $jadwalPiket): bool
    {
        if (!$authUser->can('Update:JadwalPiket')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // For Unit Admins: Check Unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $jadwalPiket->user?->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function delete(AuthUser $authUser, JadwalPiket $jadwalPiket): bool
    {
        if (!$authUser->can('Delete:JadwalPiket')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Unit Admins check
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $jadwalPiket->user?->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }
}
