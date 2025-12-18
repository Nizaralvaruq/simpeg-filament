<?php

namespace Modules\Kepegawaian\Policies;

use App\Models\User;
use Modules\Kepegawaian\Models\DataInduk;
use Illuminate\Auth\Access\HandlesAuthorization;

class DataIndukPolicy
{
    use HandlesAuthorization;

    /**
     * Super admin bypass semua.
     */
    public function before(User $user, string $ability): bool|null
    {
        return $user->hasRole('super_admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:DataInduk');
    }

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
    {
        return $user->can('Create:DataInduk');
    }

    public function update(User $user, DataInduk $dataInduk): bool
    {
        return $user->can('Update:DataInduk');
    }

    public function delete(User $user, DataInduk $dataInduk): bool
    {
        return $user->can('Delete:DataInduk');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:DataInduk');
    }

    public function restore(User $user, DataInduk $dataInduk): bool
    {
        return $user->can('Restore:DataInduk');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:DataInduk');
    }

    public function forceDelete(User $user, DataInduk $dataInduk): bool
    {
        return $user->can('ForceDelete:DataInduk');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:DataInduk');
    }
}
