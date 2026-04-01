<?php

declare(strict_types=1);

namespace Modules\Inventory\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Inventory\Models\StockTransaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockTransactionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockTransaction');
    }

    public function view(AuthUser $authUser, StockTransaction $stockTransaction): bool
    {
        return $authUser->can('View:StockTransaction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockTransaction');
    }

    public function update(AuthUser $authUser, StockTransaction $stockTransaction): bool
    {
        return $authUser->can('Update:StockTransaction');
    }

    public function delete(AuthUser $authUser, StockTransaction $stockTransaction): bool
    {
        return $authUser->can('Delete:StockTransaction');
    }

}