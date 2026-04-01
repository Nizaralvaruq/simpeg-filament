<?php

declare(strict_types=1);

namespace Modules\CBT\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\CBT\Models\ExamSession;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamSessionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamSession');
    }

    public function view(AuthUser $authUser, ExamSession $examSession): bool
    {
        return $authUser->can('View:ExamSession');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamSession');
    }

    public function update(AuthUser $authUser, ExamSession $examSession): bool
    {
        return $authUser->can('Update:ExamSession');
    }

    public function delete(AuthUser $authUser, ExamSession $examSession): bool
    {
        return $authUser->can('Delete:ExamSession');
    }

}