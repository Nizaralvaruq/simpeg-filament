<?php

declare(strict_types=1);

namespace Modules\PenilaianKinerja\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PenilaianKinerja\Models\MyAppraisalAssignment;
use Illuminate\Auth\Access\HandlesAuthorization;

class MyAppraisalAssignmentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MyAppraisalAssignment');
    }

    public function view(AuthUser $authUser, MyAppraisalAssignment $myAppraisalAssignment): bool
    {
        return $authUser->can('View:MyAppraisalAssignment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MyAppraisalAssignment');
    }

    public function update(AuthUser $authUser, MyAppraisalAssignment $myAppraisalAssignment): bool
    {
        return $authUser->can('Update:MyAppraisalAssignment');
    }

    public function delete(AuthUser $authUser, MyAppraisalAssignment $myAppraisalAssignment): bool
    {
        return $authUser->can('Delete:MyAppraisalAssignment');
    }

    public function restore(AuthUser $authUser, MyAppraisalAssignment $myAppraisalAssignment): bool
    {
        return $authUser->can('Restore:MyAppraisalAssignment');
    }

    public function forceDelete(AuthUser $authUser, MyAppraisalAssignment $myAppraisalAssignment): bool
    {
        return $authUser->can('ForceDelete:MyAppraisalAssignment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MyAppraisalAssignment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MyAppraisalAssignment');
    }

    public function replicate(AuthUser $authUser, MyAppraisalAssignment $myAppraisalAssignment): bool
    {
        return $authUser->can('Replicate:MyAppraisalAssignment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MyAppraisalAssignment');
    }

}