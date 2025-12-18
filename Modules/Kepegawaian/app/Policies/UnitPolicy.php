<?php

namespace Modules\Kepegawaian\Policies;

use App\Models\User;
use Modules\MasterData\Models\Unit;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, Unit $unit): bool
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Unit $unit): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Unit $unit): bool
    {
        return $user->hasRole('super_admin');
    }
}
