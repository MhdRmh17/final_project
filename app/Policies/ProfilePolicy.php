<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;

class ProfilePolicy
{
    public function viewAny(User $user): bool
    {
        // الطالب يشوف بس بروفايله
        return true;
    }

    public function view(User $user, Profile $profile): bool
    {
        return $profile->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        // كل طالب مسجّل يقدر ينشئ بروفايل
        return true;
    }

    public function update(User $user, Profile $profile): bool
    {
        // فقط صاحب البروفايل
        return $profile->user_id === $user->id;
    }

    public function delete(User $user, Profile $profile): bool
    {
        return $profile->user_id === $user->id;
    }
}
