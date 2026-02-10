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
        if (!$authUser->can('View:DataInduk')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        if ($authUser->hasRole('staff')) {
            return $dataInduk->user_id === $authUser->id;
        }

        // For Unit Admins, Koor Jenjang, Kepala Sekolah: Check Unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();
            return $dataInduk->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DataInduk');
    }

    public function update(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        if (!$authUser->can('Update:DataInduk')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Staff can only update themselves (if they have Update permission)
        if ($authUser->hasRole('staff')) {
            return $dataInduk->user_id === $authUser->id;
        }

        // For Unit Admins: Check Unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();
            return $dataInduk->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function delete(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        if (!$authUser->can('Delete:DataInduk')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Only super_admin/ketua_psdm should typically delete, but if unit admin has permission:
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();
            return $dataInduk->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }
}
