<?php

namespace App\Policies;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization; //me

class CommonPolicy
{
    use HandlesAuthorization;
    //public function __construct() { // }

    public function before(User $user)
    {
        if ($user->hasRole('admin') || $user->hasRole('injection_director')) {
            return true;
        }
    }
}
