<?php

declare(strict_types=1);

namespace Modules\Kepegawaian\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Kepegawaian\Models\Resign;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResignPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
<<<<<<< HEAD
        return $authUser->can('ViewAny:Resign');
=======
        return $user->hasAnyRole(['super_admin', 'kepala_sekolah', 'koor_jenjang', 'staff']);
>>>>>>> origin/branch_dhevi
    }

    public function view(AuthUser $authUser, Resign $resign): bool
    {
<<<<<<< HEAD
        return $authUser->can('View:Resign');
=======
        return $user->hasAnyRole(['super_admin', 'kepala_sekolah', 'koor_jenjang']) || $user->id === $resign->employee?->user_id;
>>>>>>> origin/branch_dhevi
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Resign');
    }

    public function update(AuthUser $authUser, Resign $resign): bool
    {
<<<<<<< HEAD
        return $authUser->can('Update:Resign');
=======
        if ($user->hasAnyRole(['super_admin', 'kepala_sekolah'])) {
            return true;
        }

        // Staff can cannot update once submitted (unless we add logic for 'draft')
        return false;
>>>>>>> origin/branch_dhevi
    }

    public function delete(AuthUser $authUser, Resign $resign): bool
    {
<<<<<<< HEAD
        return $authUser->can('Delete:Resign');
=======
        return $user->hasRole('super_admin');
>>>>>>> origin/branch_dhevi
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