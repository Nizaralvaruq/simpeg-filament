<?php

declare(strict_types=1);

namespace Modules\Akademik\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Akademik\Models\AbsensiKegiatanSiswa;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsensiKegiatanSiswaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AbsensiKegiatanSiswa');
    }

    public function view(AuthUser $authUser, AbsensiKegiatanSiswa $absensiKegiatanSiswa): bool
    {
        return $authUser->can('View:AbsensiKegiatanSiswa');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AbsensiKegiatanSiswa');
    }

    public function update(AuthUser $authUser, AbsensiKegiatanSiswa $absensiKegiatanSiswa): bool
    {
        return $authUser->can('Update:AbsensiKegiatanSiswa');
    }

    public function delete(AuthUser $authUser, AbsensiKegiatanSiswa $absensiKegiatanSiswa): bool
    {
        return $authUser->can('Delete:AbsensiKegiatanSiswa');
    }

}