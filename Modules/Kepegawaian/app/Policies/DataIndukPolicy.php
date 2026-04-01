<?php

declare(strict_types=1);

namespace Modules\Kepegawaian\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Kepegawaian\Models\DataInduk;
use Illuminate\Auth\Access\HandlesAuthorization;

class DataIndukPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DataInduk');
    }

    public function view(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        return $authUser->can('View:DataInduk');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DataInduk');
    }

    public function update(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        return $authUser->can('Update:DataInduk');
    }

    public function delete(AuthUser $authUser, DataInduk $dataInduk): bool
    {
        return $authUser->can('Delete:DataInduk');
    }

}