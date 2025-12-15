<?php

namespace Modules\Kepegawaian\Policies;

use App\Models\User;
use Modules\Kepegawaian\Models\Unit;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin_hr');
    }

    public function view(User $user, Unit $unit): bool
    {
        return $user->hasRole('admin_hr');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin_hr');
    }

    public function update(User $user, Unit $unit): bool
    {
        return $user->hasRole('admin_hr');
    }

    public function delete(User $user, Unit $unit): bool
    {
        return $user->hasRole('admin_hr');
    }
}
