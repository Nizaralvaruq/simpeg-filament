<?php

declare(strict_types=1);

namespace Modules\Akademik\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Akademik\Models\SetoranNgaji;
use Illuminate\Auth\Access\HandlesAuthorization;

class SetoranNgajiPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SetoranNgaji');
    }

    public function view(AuthUser $authUser, SetoranNgaji $setoranNgaji): bool
    {
        return $authUser->can('View:SetoranNgaji');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SetoranNgaji');
    }

    public function update(AuthUser $authUser, SetoranNgaji $setoranNgaji): bool
    {
        return $authUser->can('Update:SetoranNgaji');
    }

    public function delete(AuthUser $authUser, SetoranNgaji $setoranNgaji): bool
    {
        return $authUser->can('Delete:SetoranNgaji');
    }

}