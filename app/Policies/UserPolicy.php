<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->user_type === 'admin';
    }

    public function view(User $user, User $model)
    {
        return $user->id === $model->id || $user->user_type === 'admin';
    }

    public function update(User $user, User $model)
    {
        return $user->id === $model->id || $user->user_type === 'admin';
    }

    public function delete(User $user, User $model)
    {
        return $user->id === $model->id || $user->user_type === 'admin';
    }
    public function create(User $user)
    {
        return $user->user_type === 'admin';
    }
}
