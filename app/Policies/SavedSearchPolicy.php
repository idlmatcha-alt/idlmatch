<?php

namespace App\Policies;

use App\Models\SavedSearch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SavedSearchPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SavedSearch  $savedSearch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SavedSearch $savedSearch)
    {
        return $user->profile && $user->profile->id === $savedSearch->profile_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->profile !== null;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SavedSearch  $savedSearch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SavedSearch $savedSearch)
    {
        return $user->profile && $user->profile->id === $savedSearch->profile_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SavedSearch  $savedSearch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SavedSearch $savedSearch)
    {
        return $user->profile && $user->profile->id === $savedSearch->profile_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SavedSearch  $savedSearch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, SavedSearch $savedSearch)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SavedSearch  $savedSearch
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, SavedSearch $savedSearch)
    {
        return false;
    }
}
