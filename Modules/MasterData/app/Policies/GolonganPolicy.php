<?php

declare(strict_types=1);

namespace Modules\MasterData\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\MasterData\Models\Golongan;
use Illuminate\Auth\Access\HandlesAuthorization;

class GolonganPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Golongan');
    }

    public function view(AuthUser $authUser, Golongan $golongan): bool
    {
        return $authUser->can('View:Golongan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Golongan');
    }

    public function update(AuthUser $authUser, Golongan $golongan): bool
    {
        return $authUser->can('Update:Golongan');
    }

    public function delete(AuthUser $authUser, Golongan $golongan): bool
    {
        return $authUser->can('Delete:Golongan');
    }

}