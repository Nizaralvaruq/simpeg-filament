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

        if (!$authUser->can('View:DataInduk')) {
            return false;
        }

        // For Unit Admins, Koor Jenjang, Kepala Sekolah: Check Unit
        if ($authUser->employee) {
            // Load units if not already loaded
            if (!$authUser->employee->relationLoaded('units')) {
                $authUser->employee->load('units');
            }

            if ($authUser->employee->units->isNotEmpty()) {
                $myUnitIds = $authUser->employee->units->pluck('id')->toArray();

                // Ensure dataInduk's units are checked properly
                return $dataInduk->units()->whereIn('units.id', $myUnitIds)->exists();
            }
        }

        return false;
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

        if (!$authUser->can('Update:DataInduk')) {
            return false;
        }

        // For Unit Admins: Check Unit
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
