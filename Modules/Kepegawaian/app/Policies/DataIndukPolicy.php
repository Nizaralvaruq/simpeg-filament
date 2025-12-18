<?php

declare(strict_types=1);

namespace Modules\Kepegawaian\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Kepegawaian\Models\DataInduk;
use Illuminate\Auth\Access\HandlesAuthorization;

class DataIndukPolicy
{
    use HandlesAuthorization;
<<<<<<< HEAD
    
    public function viewAny(AuthUser $authUser): bool
=======

    /**
     * Super admin bypass semua.
     */
    public function before(User $user, string $ability): bool|null
    {
        return $user->hasRole('super_admin') ? true : null;
    }

    public function viewAny(User $user): bool
>>>>>>> origin/branch_dhevi
    {
        return $authUser->can('ViewAny:DataInduk');
    }

<<<<<<< HEAD
    public function view(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        return $authUser->can('View:DataInduk');
    }

    public function create(AuthUser $authUser): bool
=======
    public function view(User $user, DataInduk $dataInduk): bool
    {
        if (! $user->can('View:DataInduk')) {
            return false;
        }

        if ($user->hasRole('kepala_sekolah')) {
            if (! $user->employee) return false;

            $userUnitIds = $user->employee->units->pluck('id');

            return $dataInduk->units()
                ->whereIn('units.id', $userUnitIds)
                ->exists();
        }

        if ($user->hasRole('staff')) {
            return $user->employee && $user->employee->id === $dataInduk->id;
        }

        return true;
    }

    public function create(User $user): bool
>>>>>>> origin/branch_dhevi
    {
        return $authUser->can('Create:DataInduk');
    }

<<<<<<< HEAD
    public function update(AuthUser $authUser, DataInduk $dataInduk): bool
=======
    public function update(User $user, DataInduk $dataInduk): bool
>>>>>>> origin/branch_dhevi
    {
        return $authUser->can('Update:DataInduk');
    }

<<<<<<< HEAD
    public function delete(AuthUser $authUser, DataInduk $dataInduk): bool
=======
    public function delete(User $user, DataInduk $dataInduk): bool
>>>>>>> origin/branch_dhevi
    {
        return $authUser->can('Delete:DataInduk');
    }

<<<<<<< HEAD
    public function restore(AuthUser $authUser, DataInduk $dataInduk): bool
=======
    public function deleteAny(User $user): bool
>>>>>>> origin/branch_dhevi
    {
        return $authUser->can('Restore:DataInduk');
    }

<<<<<<< HEAD
    public function forceDelete(AuthUser $authUser, DataInduk $dataInduk): bool
=======
    public function restore(User $user, DataInduk $dataInduk): bool
>>>>>>> origin/branch_dhevi
    {
        return $authUser->can('ForceDelete:DataInduk');
    }

<<<<<<< HEAD
    public function forceDeleteAny(AuthUser $authUser): bool
=======
    public function restoreAny(User $user): bool
>>>>>>> origin/branch_dhevi
    {
        return $authUser->can('ForceDeleteAny:DataInduk');
    }

<<<<<<< HEAD
    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DataInduk');
    }

    public function replicate(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        return $authUser->can('Replicate:DataInduk');
    }

    public function reorder(AuthUser $authUser): bool
=======
    public function forceDelete(User $user, DataInduk $dataInduk): bool
>>>>>>> origin/branch_dhevi
    {
        return $authUser->can('Reorder:DataInduk');
    }

<<<<<<< HEAD
}
=======
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:DataInduk');
    }
}
>>>>>>> origin/branch_dhevi
