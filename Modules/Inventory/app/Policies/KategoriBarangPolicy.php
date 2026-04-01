<?php

declare(strict_types=1);

namespace Modules\Inventory\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Inventory\Models\KategoriBarang;
use Illuminate\Auth\Access\HandlesAuthorization;

class KategoriBarangPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KategoriBarang');
    }

    public function view(AuthUser $authUser, KategoriBarang $kategoriBarang): bool
    {
        return $authUser->can('View:KategoriBarang');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KategoriBarang');
    }

    public function update(AuthUser $authUser, KategoriBarang $kategoriBarang): bool
    {
        return $authUser->can('Update:KategoriBarang');
    }

    public function delete(AuthUser $authUser, KategoriBarang $kategoriBarang): bool
    {
        return $authUser->can('Delete:KategoriBarang');
    }

}