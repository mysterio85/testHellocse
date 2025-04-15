<?php

namespace App\Domain\Comment\Http\Controllers\Api;

use App\Domain\Comment\Http\Requests\StoreCommentRequest;
use App\Domain\Comment\Services\CommentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(protected CommentService $commentService)
    {
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        return $this->commentService->createComment($request);
    }
}
