<?php

declare(strict_types=1);

namespace Modules\CBT\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\CBT\Models\Exam;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Exam');
    }

    public function view(AuthUser $authUser, Exam $exam): bool
    {
        return $authUser->can('View:Exam');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Exam');
    }

    public function update(AuthUser $authUser, Exam $exam): bool
    {
        return $authUser->can('Update:Exam');
    }

    public function delete(AuthUser $authUser, Exam $exam): bool
    {
        return $authUser->can('Delete:Exam');
    }

}