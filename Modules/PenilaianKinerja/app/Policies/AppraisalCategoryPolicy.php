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
        /** @var \App\Models\User $authUser */
        return $authUser->can('ViewAny:AppraisalCategory') || $authUser->hasRole('staff');
    }

    public function view(AuthUser $authUser, AppraisalCategory $appraisalCategory): bool
    {
        /** @var \App\Models\User $authUser */
        return $authUser->can('View:AppraisalCategory') || $authUser->hasRole('staff');
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
