<?php

declare(strict_types=1);

namespace Modules\Inventory\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Inventory\Models\PermintaanBarang;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermintaanBarangPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PermintaanBarang');
    }

    public function view(AuthUser $authUser, PermintaanBarang $permintaanBarang): bool
    {
        return $authUser->can('View:PermintaanBarang');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PermintaanBarang');
    }

    public function update(AuthUser $authUser, PermintaanBarang $permintaanBarang): bool
    {
        return $authUser->can('Update:PermintaanBarang');
    }

    public function delete(AuthUser $authUser, PermintaanBarang $permintaanBarang): bool
    {
        return $authUser->can('Delete:PermintaanBarang');
    }

}