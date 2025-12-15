<?php

namespace Modules\Kepegawaian\Policies;

use App\Models\User;
use Modules\Kepegawaian\Models\DataInduk;
use Illuminate\Auth\Access\HandlesAuthorization;

class DataIndukPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:DataInduk');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DataInduk $dataInduk): bool
    {
        return $user->can('View:DataInduk');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:DataInduk');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DataInduk $dataInduk): bool
    {
        return $user->can('Update:DataInduk');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DataInduk $dataInduk): bool
    {
        return $user->can('Delete:DataInduk');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:DataInduk');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DataInduk $dataInduk): bool
    {
        return $user->can('Restore:DataInduk');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:DataInduk');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, DataInduk $dataInduk): bool
    {
        return $user->can('Replicate:DataInduk');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:DataInduk');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DataInduk $dataInduk): bool
    {
        return $user->can('ForceDelete:DataInduk');
    }

    /**
     * Determine whether the user can bulk permanently delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:DataInduk');
    }
}
