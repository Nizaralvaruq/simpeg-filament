<?php

declare(strict_types=1);

namespace Modules\Presensi\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Presensi\Models\JadwalPiket;
use Illuminate\Auth\Access\HandlesAuthorization;

class JadwalPiketPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JadwalPiket');
    }

    public function view(AuthUser $authUser, JadwalPiket $jadwalPiket): bool
    {
        return $authUser->can('View:JadwalPiket');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JadwalPiket');
    }

    public function update(AuthUser $authUser, JadwalPiket $jadwalPiket): bool
    {
        return $authUser->can('Update:JadwalPiket');
    }

    public function delete(AuthUser $authUser, JadwalPiket $jadwalPiket): bool
    {
        return $authUser->can('Delete:JadwalPiket');
    }

}