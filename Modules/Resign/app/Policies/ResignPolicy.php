<?php

declare(strict_types=1);

namespace Modules\Resign\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Resign\Models\Resign;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResignPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Resign');
    }

    public function view(AuthUser $authUser, Resign $resign): bool
    {
        return $authUser->can('View:Resign');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Resign');
    }

    public function update(AuthUser $authUser, Resign $resign): bool
    {
        return $authUser->can('Update:Resign');
    }

    public function delete(AuthUser $authUser, Resign $resign): bool
    {
        return $authUser->can('Delete:Resign');
    }

}