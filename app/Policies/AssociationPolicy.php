<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Association;

class AssociationPolicy
{
    /**
     * Allow admin or the owner association itself to update.
     */
    public function update(User $user, Association $association): bool
    {

        return $user->user_type === 'admin' || $user->id === $association->user_id;
    }

    /**
     * Allow admin or the owner association itself to delete.
     */
    public function delete(User $user, Association $association): bool
    {
        return $user->user_type === 'admin' || $user->id === $association->user_id;
    }

    /**
     * Allow anyone to view.
     */
    public function view(User $user, Association $association): bool
    {
        return true;
    }
}
