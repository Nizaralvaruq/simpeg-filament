<?php

declare(strict_types=1);

namespace Modules\Presensi\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Presensi\Models\Absensi;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsensiPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Absensi');
    }

    public function view(AuthUser $authUser, Absensi $absensi): bool
    {
        return $authUser->can('View:Absensi');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Absensi');
    }

    public function update(AuthUser $authUser, Absensi $absensi): bool
    {
        return $authUser->can('Update:Absensi');
    }

    public function delete(AuthUser $authUser, Absensi $absensi): bool
    {
        return $authUser->can('Delete:Absensi');
    }

}