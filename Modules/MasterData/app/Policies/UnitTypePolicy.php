<?php

declare(strict_types=1);

namespace Modules\MasterData\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\MasterData\Models\UnitType;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UnitType');
    }

    public function view(AuthUser $authUser, UnitType $unitType): bool
    {
        return $authUser->can('View:UnitType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UnitType');
    }

    public function update(AuthUser $authUser, UnitType $unitType): bool
    {
        return $authUser->can('Update:UnitType');
    }

    public function delete(AuthUser $authUser, UnitType $unitType): bool
    {
        return $authUser->can('Delete:UnitType');
    }

}