<?php

namespace App\Domain\Comment\Http\Controllers\Api;

use App\Domain\Comment\Http\Requests\StoreCommentRequest;
use App\Domain\Comment\Models\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request): JsonResponse
    {
        $exists = Comment::where('administrator_id', auth()->id())
            ->where('profile_id', $request->profile_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Profil dejà commenté.'
            ], 409);
        }
        $request->validated();

        $comment = Comment::create([
            'content' => $request->text,
            'administrator_id' => auth()->id(),
            'profile_id' => $request->profile_id,
        ]);

        return response()->json($comment, 201);
    }
}
