<?php

declare(strict_types=1);

namespace Modules\CBT\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\CBT\Models\Question;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Question');
    }

    public function view(AuthUser $authUser, Question $question): bool
    {
        return $authUser->can('View:Question');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Question');
    }

    public function update(AuthUser $authUser, Question $question): bool
    {
        return $authUser->can('Update:Question');
    }

    public function delete(AuthUser $authUser, Question $question): bool
    {
        return $authUser->can('Delete:Question');
    }

}