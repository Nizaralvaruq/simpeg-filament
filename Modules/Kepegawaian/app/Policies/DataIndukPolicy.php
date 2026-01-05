<?php

declare(strict_types=1);

namespace Modules\Kepegawaian\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Kepegawaian\Models\DataInduk;
use Illuminate\Auth\Access\HandlesAuthorization;

class DataIndukPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DataInduk');
    }

    public function view(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        /** @var \App\Models\User $authUser */
        if ($authUser->can('View:DataInduk')) {
            // Staff filter
            if ($authUser->hasRole('staff')) {
                return $authUser->employee && $authUser->employee->id === $dataInduk->id;
            }
            return true;
        }

        return $authUser->employee && $authUser->employee->id === $dataInduk->id;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DataInduk');
    }

    public function update(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        return $authUser->can('Update:DataInduk');
    }

    public function delete(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        return $authUser->can('Delete:DataInduk');
    }

    public function restore(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        return $authUser->can('Restore:DataInduk');
    }

    public function forceDelete(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        return $authUser->can('ForceDelete:DataInduk');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DataInduk');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DataInduk');
    }

    public function replicate(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        return $authUser->can('Replicate:DataInduk');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DataInduk');
    }
}
