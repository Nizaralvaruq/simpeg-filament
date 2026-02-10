<?php

declare(strict_types=1);

namespace Modules\MasterData\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\MasterData\Models\Unit;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Unit');
    }

    public function view(AuthUser $authUser, Unit $unit): bool
    {
        if (!$authUser->can('View:Unit')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasRole('super_admin')) {
            return true;
        }

        if ($authUser->hasRole('admin_unit') && $authUser->employee) {
            return $authUser->employee->units->contains('id', $unit->id);
        }

        return false;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Unit');
    }

    public function update(AuthUser $authUser, Unit $unit): bool
    {
        if (!$authUser->can('Update:Unit')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasRole('super_admin')) {
            return true;
        }

        if ($authUser->hasRole('admin_unit') && $authUser->employee) {
            return $authUser->employee->units->contains('id', $unit->id);
        }

        return false;
    }

    public function delete(AuthUser $authUser, Unit $unit): bool
    {
        if (!$authUser->can('Delete:Unit')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasRole('super_admin')) {
            return true;
        }

        if ($authUser->hasRole('admin_unit') && $authUser->employee) {
            return $authUser->employee->units->contains('id', $unit->id);
        }

        return false;
    }
}
