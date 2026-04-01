<?php

declare(strict_types=1);

namespace Modules\Presensi\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Presensi\Models\Kegiatan;
use Illuminate\Auth\Access\HandlesAuthorization;

class KegiatanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Kegiatan');
    }

    public function view(AuthUser $authUser, Kegiatan $kegiatan): bool
    {
        return $authUser->can('View:Kegiatan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Kegiatan');
    }

    public function update(AuthUser $authUser, Kegiatan $kegiatan): bool
    {
        return $authUser->can('Update:Kegiatan');
    }

    public function delete(AuthUser $authUser, Kegiatan $kegiatan): bool
    {
        return $authUser->can('Delete:Kegiatan');
    }

}