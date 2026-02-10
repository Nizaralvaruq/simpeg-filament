<?php

declare(strict_types=1);

namespace Modules\Resign\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Resign\Models\Resign;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResignPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Resign');
    }

    public function view(AuthUser $authUser, Resign $resign): bool
    {
        if (!$authUser->can('View:Resign')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        if ($authUser->hasRole('staff')) {
            return $resign->employee && $resign->employee->user_id === $authUser->id;
        }

        // For Unit Admins: Check if target employee belongs to my unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $resign->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Resign');
    }

    public function update(AuthUser $authUser, Resign $resign): bool
    {
        if (!$authUser->can('Update:Resign')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Staff can only update their own records if pending (diajukan)
        if ($authUser->hasRole('staff')) {
            return $resign->employee && $resign->employee->user_id === $authUser->id && $resign->status === 'diajukan';
        }

        // For Unit Admins: Check Unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $resign->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function delete(AuthUser $authUser, Resign $resign): bool
    {
        if (!$authUser->can('Delete:Resign')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Unit Admins check
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $resign->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }
}
