<?php

declare(strict_types=1);

namespace Modules\PenilaianKinerja\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppraisalAssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        /** @var \App\Models\User $authUser */
        return $authUser->can('ViewAny:AppraisalAssignment') || $authUser->hasRole('staff');
    }

    public function view(AuthUser $authUser, AppraisalAssignment $appraisalAssignment): bool
    {
        /** @var \App\Models\User $authUser */
        // Rater can always view their assignment
        if ($appraisalAssignment->rater_id === $authUser->id) {
            return true;
        }

        if (!$authUser->can('View:AppraisalAssignment')) {
            return false;
        }

        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // For Unit Admins: Check if ratee belongs to my unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $appraisalAssignment->ratee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AppraisalAssignment');
    }

    public function update(AuthUser $authUser, AppraisalAssignment $appraisalAssignment): bool
    {
        if (!$authUser->can('Update:AppraisalAssignment')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Mid-level admins can update if ratee is in their unit
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $appraisalAssignment->ratee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }

    public function delete(AuthUser $authUser, AppraisalAssignment $appraisalAssignment): bool
    {
        if (!$authUser->can('Delete:AppraisalAssignment')) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        if ($authUser->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return true;
        }

        // Unit Admins check
        if ($authUser->employee && $authUser->employee->units->isNotEmpty()) {
            $myUnitIds = $authUser->employee->units->pluck('id')->all();

            $targetEmployee = $appraisalAssignment->ratee;
            if (!$targetEmployee) return false;

            return $targetEmployee->units()->whereIn('units.id', $myUnitIds)->exists();
        }

        return false;
    }
}
