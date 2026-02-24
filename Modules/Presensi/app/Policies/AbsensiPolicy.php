<?php

declare(strict_types=1);

namespace Modules\Presensi\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Presensi\Models\Absensi;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsensiPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        /** @var \App\Models\User $authUser */
        return $authUser->can('ViewAny:Absensi') || $authUser->hasRole('staff');
    }

    public function view(AuthUser $authUser, Absensi $absensi): bool
    {
        /** @var \App\Models\User $authUser */
        if ($authUser->hasRole('staff')) {
            return (int) $absensi->user_id === (int) $authUser->id;
        }

        if (!$authUser->can('View:Absensi')) {
            return false;
        }

        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // For Unit Admins: Check if target employee belongs to my unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $absensi->user?->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Absensi');
    }

    public function update(AuthUser $authUser, Absensi $absensi): bool
    {
        /** @var \App\Models\User $authUser */
        if ($authUser->hasRole('staff')) {
            return (int) $absensi->user_id === (int) $authUser->id;
        }

        if (!$authUser->can('Update:Absensi')) {
            return false;
        }

        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // For Unit Admins: Check Unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $absensi->user?->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function delete(AuthUser $authUser, Absensi $absensi): bool
    {
        if (!$authUser->can('Delete:Absensi')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Unit Admins check
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $absensi->user?->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }
}
