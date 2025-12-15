<?php

namespace Modules\Pegawai\Policies;

use App\Models\User;
use Modules\Pegawai\Models\LeaveRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeaveRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin_hr', 'kepala_sekolah', 'koor_jenjang', 'staff']);
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasAnyRole(['admin_hr', 'kepala_sekolah', 'koor_jenjang']) || $user->id === $leaveRequest->employee?->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('staff');
    }

    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasAnyRole(['admin_hr', 'kepala_sekolah']);
    }

    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasRole('admin_hr');
    }
}
