<?php

namespace Modules\Pegawai\Policies;

use App\Models\User;
use Modules\Pegawai\Models\DataInduk;
use Illuminate\Auth\Access\HandlesAuthorization;

class PegawaiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin_hr', 'kepala_sekolah', 'koor_jenjang', 'staff']);
    }

    public function view(User $user, DataInduk $dataInduk): bool
    {
        if ($user->hasRole('admin_hr')) {
            return true;
        }

        if ($user->hasRole('kepala_sekolah')) {
            // Allow if in same unit
            // Note: This logic is expensive in a loop, but Policy is checked per item.
            // The Scope filters the list, so this is a secondary check for direct access.

            if (!$user->employee) return false;

            // Get user units IDs
            $userUnitIds = $user->employee->units->pluck('id');
            // Check overlap
            return $dataInduk->units->whereIn('id', $userUnitIds)->isNotEmpty();
        }

        if ($user->hasRole('staff')) {
            return $user->employee && $user->employee->id === $dataInduk->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin_hr', 'koor_jenjang']);
    }

    public function update(User $user, DataInduk $dataInduk): bool
    {
        return $user->hasAnyRole(['admin_hr', 'koor_jenjang']);
    }

    public function delete(User $user, DataInduk $dataInduk): bool
    {
        return $user->hasAnyRole(['admin_hr']);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin_hr');
    }

    public function restore(User $user, DataInduk $dataInduk): bool
    {
        return $user->hasRole('admin_hr');
    }

    public function forceDelete(User $user, DataInduk $dataInduk): bool
    {
        return $user->hasRole('admin_hr');
    }
}
