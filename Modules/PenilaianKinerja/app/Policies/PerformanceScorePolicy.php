<?php

declare(strict_types=1);

namespace Modules\PenilaianKinerja\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PenilaianKinerja\Models\PerformanceScore;
use Illuminate\Auth\Access\HandlesAuthorization;

class PerformanceScorePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PerformanceScore');
    }

    public function view(AuthUser $authUser, PerformanceScore $performanceScore): bool
    {
        return $authUser->can('View:PerformanceScore');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PerformanceScore');
    }

    public function update(AuthUser $authUser, PerformanceScore $performanceScore): bool
    {
        return $authUser->can('Update:PerformanceScore');
    }

    public function delete(AuthUser $authUser, PerformanceScore $performanceScore): bool
    {
        return $authUser->can('Delete:PerformanceScore');
    }

}