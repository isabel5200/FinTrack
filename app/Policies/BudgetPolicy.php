<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BudgetPolicty
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Budget $budget): bool
    {
        return $user->id === $budget->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Budget $budget): bool
    {
        return $user->id === $budget->user_id;
    }

    public function delete(User $user, Budget $budget): bool
    {
        return $user->id === $budget->user_id;
    }

    public function restore(User $user, Budget $budget): bool
    {
        return false;
    }

    public function forceDelete(User $user, Budget $budget): bool
    {
        return false;
    }
}
