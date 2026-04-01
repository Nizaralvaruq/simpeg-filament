<?php

declare(strict_types=1);

namespace Modules\Retirement\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Retirement\Models\Retirement;
use Illuminate\Auth\Access\HandlesAuthorization;

class RetirementPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Retirement');
    }

    public function view(AuthUser $authUser, Retirement $retirement): bool
    {
        return $authUser->can('View:Retirement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Retirement');
    }

    public function update(AuthUser $authUser, Retirement $retirement): bool
    {
        return $authUser->can('Update:Retirement');
    }

    public function delete(AuthUser $authUser, Retirement $retirement): bool
    {
        return $authUser->can('Delete:Retirement');
    }

}