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
        /** @var \App\Models\User $authUser */
        return $authUser->can('ViewAny:DataInduk') || $authUser->employee !== null;
    }

    public function view(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        /** @var \App\Models\User $authUser */

        // Let super admins through immediately
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Always allow users to see their own biodata
        if ((int) $dataInduk->user_id === (int) $authUser->id) {
            return true;
        }

        // For Unit Admins, Koor Jenjang, Kepala Sekolah: Check Unit first before Spatie permission
        if ($authUser->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah']) && $authUser->employee) {
            if (!$authUser->employee->relationLoaded('units')) {
                $authUser->employee->load('units');
            }

            if ($authUser->employee->units->isNotEmpty()) {
                $myUnitIds = $authUser->employee->units->pluck('id')->toArray();
                // If the employee they are trying to view is in one of their units, allow it
                if ($dataInduk->units()->whereIn('units.id', $myUnitIds)->exists()) {
                    return true;
                }
            }
        }

        // Fallback to Spatie permission for other custom roles
        return $authUser->can('View:DataInduk');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DataInduk');
    }

    public function update(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        /** @var \App\Models\User $authUser */

        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Always allow users to update their own biodata
        if ((int) $dataInduk->user_id === (int) $authUser->id) {
            return true;
        }

        // For Admin Unit, they can only edit their OWN biodata (handled above).
        // They CANNOT edit other people's biodata within their unit.
        if ($authUser->hasRole('admin_unit')) {
            return false;
        }

        return $authUser->can('Update:DataInduk');
    }

    public function delete(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        if (!$authUser->can('Delete:DataInduk')) {
            return false;
        }

        // Only super_admin/ketua_psdm should typically delete, but if unit admin has permission:
        if ($authUser->employee) {
            if (!$authUser->employee->relationLoaded('units')) {
                $authUser->employee->load('units');
            }

            if ($authUser->employee->units->isNotEmpty()) {
                $myUnitIds = $authUser->employee->units->pluck('id')->toArray();
                return $dataInduk->units()->whereIn('units.id', $myUnitIds)->exists();
            }
        }

        return false;
    }
}
