<?php

declare(strict_types=1);

namespace Modules\Inventory\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Inventory\Models\Barang;
use Illuminate\Auth\Access\HandlesAuthorization;

class BarangPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Barang');
    }

    public function view(AuthUser $authUser, Barang $barang): bool
    {
        return $authUser->can('View:Barang');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Barang');
    }

    public function update(AuthUser $authUser, Barang $barang): bool
    {
        return $authUser->can('Update:Barang');
    }

    public function delete(AuthUser $authUser, Barang $barang): bool
    {
        return $authUser->can('Delete:Barang');
    }

}