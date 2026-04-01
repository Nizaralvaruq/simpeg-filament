<?php

declare(strict_types=1);

namespace Modules\CBT\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\CBT\Models\QuestionBank;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionBankPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:QuestionBank');
    }

    public function view(AuthUser $authUser, QuestionBank $questionBank): bool
    {
        return $authUser->can('View:QuestionBank');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:QuestionBank');
    }

    public function update(AuthUser $authUser, QuestionBank $questionBank): bool
    {
        return $authUser->can('Update:QuestionBank');
    }

    public function delete(AuthUser $authUser, QuestionBank $questionBank): bool
    {
        return $authUser->can('Delete:QuestionBank');
    }

}