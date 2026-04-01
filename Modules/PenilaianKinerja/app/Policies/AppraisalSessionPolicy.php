<?php

declare(strict_types=1);

namespace Modules\PenilaianKinerja\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppraisalSessionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AppraisalSession');
    }

    public function view(AuthUser $authUser, AppraisalSession $appraisalSession): bool
    {
        return $authUser->can('View:AppraisalSession');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AppraisalSession');
    }

    public function update(AuthUser $authUser, AppraisalSession $appraisalSession): bool
    {
        return $authUser->can('Update:AppraisalSession');
    }

    public function delete(AuthUser $authUser, AppraisalSession $appraisalSession): bool
    {
        return $authUser->can('Delete:AppraisalSession');
    }

}