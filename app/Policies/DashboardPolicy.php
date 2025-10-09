<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class DashboardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
    /* ---------------------------------- befor --------------------------------- */
    public function before($user)
    {
        if ($user->hasRole('admin') || $user->hasRole('injection_director')) {
            return true;
        }
    }

    /* ------------- Determine if the user can access the dashboard. ------------ */
    public function accessDashboard(User $user): bool
    {
        return $user->hasRole('injection_director');
    }

    /* ------------- Determine if the user can access sales section. ------------ */
    public function accessSales(User $user): bool
    {
        return $user->hasRole('sales');
    }
    
    /* ----------- Determine if the user can access industry section. ----------- */
    public function accessIndustry(User $user): bool
    {
        return $user->hasRole('industry');
    }

    /* --------------- Determine if the user can create an order. --------------- */
    public function createOrder(User $user): bool
    {
        return $user->hasRole('sale_director') || $user->hasRole('admin');
    }
}
