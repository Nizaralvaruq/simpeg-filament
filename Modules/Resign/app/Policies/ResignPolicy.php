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
        /** @var \App\Models\User $authUser */
        if ($authUser->can('View:Resign')) {
            // Staff filter
            if ($authUser->hasRole('staff')) {
                return $authUser->id === $resign->user_id;
            }
            return true;
        }

        return false;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Resign');
    }

    public function update(AuthUser $authUser, Resign $resign): bool
    {
        return $authUser->can('Update:Resign');
    }

    public function delete(AuthUser $authUser, Resign $resign): bool
    {
        return $authUser->can('Delete:Resign');
    }

    public function restore(AuthUser $authUser, Resign $resign): bool
    {
        return $authUser->can('Restore:Resign');
    }

    public function forceDelete(AuthUser $authUser, Resign $resign): bool
    {
        return $authUser->can('ForceDelete:Resign');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Resign');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Resign');
    }

    public function replicate(AuthUser $authUser, Resign $resign): bool
    {
        return $authUser->can('Replicate:Resign');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Resign');
    }
}
