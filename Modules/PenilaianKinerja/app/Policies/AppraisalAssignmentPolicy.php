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

}