<?php

namespace Modules\Kepegawaian\Policies;

use App\Models\User;
use Modules\Kepegawaian\Models\Resign;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResignPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin_hr', 'kepala_sekolah', 'koor_jenjang', 'staff']);
    }

    public function view(User $user, Resign $resign): bool
    {
        return $user->hasAnyRole(['admin_hr', 'kepala_sekolah', 'koor_jenjang']) || $user->id === $resign->employee?->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('staff');
    }

    public function update(User $user, Resign $resign): bool
    {
        if ($user->hasAnyRole(['admin_hr', 'kepala_sekolah'])) {
            return true;
        }

        // Staff can cannot update once submitted (unless we add logic for 'draft')
        return false;
    }

    public function delete(User $user, Resign $resign): bool
    {
        return $user->hasRole('admin_hr');
    }
}
