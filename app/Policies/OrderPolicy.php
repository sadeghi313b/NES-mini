<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{

    /* ---------------------------------- befor --------------------------------- */
    public function before($user)
    {
        // dd($user->hasRole('admin'));
        if ($user->hasRole('admin') || $user->hasRole('injection_director')) {
            return true;
        }
    }

    /* --------------- Determine if the user can create an order. --------------- */
    public function create(User $user): bool
    {
        return $user->hasRole('sale_director');
    }

    public function edit(User $user, Order $order): bool
    {
        return $user->hasRole('sale_director');
    }

    public function canAccessOrder(User $user)
    {
        return $user->hasRole('sale_director');
    }
    
    public function form(User $user)
    {
        return $user->hasRole('sale_director');
    }

    public function manage(User $user, $modelOrInstance)
{
    if ($modelOrInstance instanceof Order) {
        return $user->hasRole('sale_director');
    }

    if ($modelOrInstance === Order::class) {
        return $user->hasRole('sale_director');
    }

    return false;
}
}
