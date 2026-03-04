<?php

declare(strict_types=1);

namespace Modules\Retirement\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Retirement\Models\Retirement;
use Illuminate\Auth\Access\HandlesAuthorization;

class RetirementPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        /** @var \App\Models\User $authUser */
        return $authUser->can('ViewAny:Retirement') || $authUser->hasRole('staff');
    }

    public function view(AuthUser $authUser, Retirement $retirement): bool
    {
        /** @var \App\Models\User $authUser */
        if ($authUser->hasRole('staff')) {
            return $retirement->employee && (int) $retirement->employee->user_id === (int) $authUser->id;
        }

        if (!$authUser->can('View:Retirement')) {
            return false;
        }

        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // For Unit Admins: Check if target employee belongs to my unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $retirement->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Retirement');
    }

    public function update(AuthUser $authUser, Retirement $retirement): bool
    {
        /** @var \App\Models\User $authUser */
        if ($authUser->hasRole('staff')) {
            return $retirement->employee && (int) $retirement->employee->user_id === (int) $authUser->id && $retirement->status === 'diajukan';
        }

        if (!$authUser->can('Update:Retirement')) {
            return false;
        }

        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // For Unit Admins: Check Unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $retirement->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function delete(AuthUser $authUser, Retirement $retirement): bool
    {
        if (!$authUser->can('Delete:Retirement')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Unit Admins check
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $retirement->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }
}
