<?php

declare(strict_types=1);

namespace Modules\Akademik\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Akademik\Models\Siswa;
use Illuminate\Auth\Access\HandlesAuthorization;

class SiswaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Siswa');
    }

    public function view(AuthUser $authUser, Siswa $siswa): bool
    {
        return $authUser->can('View:Siswa');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Siswa');
    }

    public function update(AuthUser $authUser, Siswa $siswa): bool
    {
        return $authUser->can('Update:Siswa');
    }

    public function delete(AuthUser $authUser, Siswa $siswa): bool
    {
        return $authUser->can('Delete:Siswa');
    }

}