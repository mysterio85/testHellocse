<?php

namespace App\Domain\Comment\Repositories;

use App\Domain\Comment\Models\Comment;

class CommentRepository
{
    public function hasAdministratorAlreadyCommented(int $administratorId, int $profileId): bool
    {
        return Comment::where('administrator_id', $administratorId)
            ->where('profile_id', $profileId)
            ->exists();
    }
}
