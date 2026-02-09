<?php

declare(strict_types=1);

namespace Modules\PenilaianKinerja\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PenilaianKinerja\Models\AppraisalCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppraisalCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AppraisalCategory');
    }

    public function view(AuthUser $authUser, AppraisalCategory $appraisalCategory): bool
    {
        return $authUser->can('View:AppraisalCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AppraisalCategory');
    }

    public function update(AuthUser $authUser, AppraisalCategory $appraisalCategory): bool
    {
        return $authUser->can('Update:AppraisalCategory');
    }

    public function delete(AuthUser $authUser, AppraisalCategory $appraisalCategory): bool
    {
        return $authUser->can('Delete:AppraisalCategory');
    }

}