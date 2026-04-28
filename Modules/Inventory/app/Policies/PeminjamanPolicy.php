<?php

declare(strict_types=1);

namespace Modules\Inventory\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Inventory\Models\Peminjaman;
use Illuminate\Auth\Access\HandlesAuthorization;

class PeminjamanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Peminjaman');
    }

    public function view(AuthUser $authUser, Peminjaman $peminjaman): bool
    {
        return $authUser->can('View:Peminjaman');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Peminjaman');
    }

    public function update(AuthUser $authUser, Peminjaman $peminjaman): bool
    {
        return $authUser->can('Update:Peminjaman');
    }

    public function delete(AuthUser $authUser, Peminjaman $peminjaman): bool
    {
        return $authUser->can('Delete:Peminjaman');
    }

}