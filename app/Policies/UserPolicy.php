<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:User');
    }

    public function view(AuthUser $authUser, User $user): bool
    {
        if (!$authUser->can('View:User')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasRole('super_admin')) {
            return true;
        }

        // Admin unit can only view users in their unit
        if ($authUser->hasRole('admin_unit') && $authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $unitIds = $authUser->employee->units->pluck('id')->all();

            // Check if target user's employee belongs to any of these units
            return $user->employee && $user->employee->units()->whereIn('units.id', $unitIds)->exists();
        }

        return $authUser->id === $user->id;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:User');
    }

    public function update(AuthUser $authUser, User $user): bool
    {
        if (!$authUser->can('Update:User')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasRole('super_admin')) {
            return true;
        }

        // Admin unit checks
        if ($authUser->hasRole('admin_unit') && $authUser->employee && $authUser->employee->units->isNotEmpty()) {
            // Cannot update high-level admins
            if ($user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah'])) {
                return false;
            }

            $unitIds = $authUser->employee->units->pluck('id')->all();
            return $user->employee && $user->employee->units()->whereIn('units.id', $unitIds)->exists();
        }

        return $authUser->id === $user->id;
    }

    public function delete(AuthUser $authUser, User $user): bool
    {
        if (!$authUser->can('Delete:User')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasRole('super_admin')) {
            // Even super admin shouldn't delete themselves easily, but policy-wise it's allowed
            return $authUser->id !== $user->id;
        }

        // Admin unit checks
        if ($authUser->hasRole('admin_unit') && $authUser->employee && $authUser->employee->units->isNotEmpty()) {
            // Cannot delete self or high-level admins
            if ($authUser->id === $user->id || $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah'])) {
                return false;
            }

            $unitIds = $authUser->employee->units->pluck('id')->all();
            return $user->employee && $user->employee->units()->whereIn('units.id', $unitIds)->exists();
        }

        return false;
    }
}
