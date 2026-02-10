<?php

declare(strict_types=1);

namespace Modules\PenilaianKinerja\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PenilaianKinerja\Models\PerformanceScore;
use Illuminate\Auth\Access\HandlesAuthorization;

class PerformanceScorePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PerformanceScore');
    }

    public function view(AuthUser $authUser, PerformanceScore $performanceScore): bool
    {
        if (!$authUser->can('View:PerformanceScore')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        if ($authUser->hasRole('staff')) {
            return $performanceScore->penilai_id === $authUser->id || $performanceScore->data_induk_id === $authUser->employee?->id;
        }

        // For Unit Admins: Check if target employee belongs to my unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $performanceScore->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PerformanceScore');
    }

    public function update(AuthUser $authUser, PerformanceScore $performanceScore): bool
    {
        if (!$authUser->can('Update:PerformanceScore')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Staff can only update their own assessments as rater
        if ($authUser->hasRole('staff')) {
            return $performanceScore->penilai_id === $authUser->id;
        }

        // For Unit Admins: Check Unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $performanceScore->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function delete(AuthUser $authUser, PerformanceScore $performanceScore): bool
    {
        if (!$authUser->can('Delete:PerformanceScore')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Unit Admins check
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $performanceScore->employee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }
}
