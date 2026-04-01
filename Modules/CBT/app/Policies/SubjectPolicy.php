<?php

declare(strict_types=1);

namespace Modules\CBT\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\CBT\Models\Subject;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Subject');
    }

    public function view(AuthUser $authUser, Subject $subject): bool
    {
        return $authUser->can('View:Subject');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Subject');
    }

    public function update(AuthUser $authUser, Subject $subject): bool
    {
        return $authUser->can('Update:Subject');
    }

    public function delete(AuthUser $authUser, Subject $subject): bool
    {
        return $authUser->can('Delete:Subject');
    }

}