<?php

namespace App\Policies;

use App\Models\ProjectForm;
use App\Models\User;

class ProjectFormPolicy
{
    public function viewAny(User $user): bool
    {
        // عوّض عن شرط الصلاحية ليُسمح للجميع
        return true;
    }

    public function view(User $user, ProjectForm $form): bool
    {
        return $user->type === 'admin' || $form->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

   public function update(User $user, ProjectForm $form): bool
{
    \Log::info("checking update:", [
        'user_id'     => $user->id,
        'form_user'   => $form->user_id,
        'user_type'   => $user->type,
    ]);

    return $user->type === 'admin' || $form->user_id === $user->id;
}


    public function delete(User $user, ProjectForm $form): bool
    {
        return $user->type === 'admin' || $form->user_id === $user->id;
    }

    public function updateStatus(User $user, ProjectForm $form): bool
    {
        return $user->type === 'admin';
    }
}
