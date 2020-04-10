<?php

namespace App\Policies;

use App\User;
use App\Traits\AdminActions;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization,AdminActions;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $authentcatedUser, User $user)
    {
        return $authentcatedUser->id === $user->id;
    }

    
    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $authentcatedUser, User $user)
    {
        return $authentcatedUser->id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $authentcatedUser
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $authentcatedUser, User $user)
    {
        return $authentcatedUser->id === $user->id && $authentcatedUser->token()->client->personal_access_client;
    }

   
}
