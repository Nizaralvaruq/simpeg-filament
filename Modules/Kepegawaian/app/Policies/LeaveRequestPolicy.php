<?php

namespace Modules\Kepegawaian\Policies;

use App\Models\User;
use Modules\Kepegawaian\Models\LeaveRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeaveRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:LeaveRequest');
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        // Owner typically needs ViewAny plus maybe View, or just scoping.
        // If we strictly use permission, we rely on Scoping to hide others.
        // But for explicit "view" action:
        return $user->can('View:LeaveRequest');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:LeaveRequest');
    }

    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('Update:LeaveRequest');
    }

    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('Delete:LeaveRequest');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:LeaveRequest');
    }
}
