<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProjectLike;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectLikePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function delete(User $user, ProjectLike $like){
        return $user->id === $like->learner_id;
    }
}
