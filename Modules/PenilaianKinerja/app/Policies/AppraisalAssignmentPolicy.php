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
        return $authUser->can('ViewAny:AppraisalAssignment');
    }

    public function view(AuthUser $authUser, AppraisalAssignment $appraisalAssignment): bool
    {
        return $authUser->can('View:AppraisalAssignment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AppraisalAssignment');
    }

    public function update(AuthUser $authUser, AppraisalAssignment $appraisalAssignment): bool
    {
        return $authUser->can('Update:AppraisalAssignment');
    }

    public function delete(AuthUser $authUser, AppraisalAssignment $appraisalAssignment): bool
    {
        return $authUser->can('Delete:AppraisalAssignment');
    }

    public function restore(AuthUser $authUser, AppraisalAssignment $appraisalAssignment): bool
    {
        return $authUser->can('Restore:AppraisalAssignment');
    }

    public function forceDelete(AuthUser $authUser, AppraisalAssignment $appraisalAssignment): bool
    {
        return $authUser->can('ForceDelete:AppraisalAssignment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AppraisalAssignment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AppraisalAssignment');
    }

    public function replicate(AuthUser $authUser, AppraisalAssignment $appraisalAssignment): bool
    {
        return $authUser->can('Replicate:AppraisalAssignment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AppraisalAssignment');
    }

}