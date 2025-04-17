<?php

namespace App\Domain\Comment\Actions;

use App\Domain\Comment\Models\Comment;

class CreateCommentAction
{
    /**
     * @param array<string, mixed> $data
     *
     * @return Comment
     */
    public function execute(array $data): Comment
    {
        return Comment::create($data);
    }
}
