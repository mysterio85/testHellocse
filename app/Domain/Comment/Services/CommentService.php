<?php

namespace App\Domain\Comment\Services;

use App\Domain\Comment\Actions\CreateCommentAction;
use App\Domain\Comment\Http\Requests\StoreCommentRequest;
use App\Domain\Comment\Repositories\CommentRepository;
use Illuminate\Http\JsonResponse;

class CommentService
{
    public function __construct(
        protected CreateCommentAction $createCommentAction,
        protected CommentRepository $commentRepository
    ) {
    }

    public function createComment(StoreCommentRequest $request): JsonResponse
    {
        $adminIdRaw   = auth()->id();
        $profileIdRaw = $request->input('profile_id');

        if (!is_numeric($adminIdRaw) || !is_numeric($profileIdRaw)) {
            return response()->json([
                'message' => 'Identifiants invalides.',
            ], 422);
        }

        $adminId   = (int) $adminIdRaw;
        $profileId = (int) $profileIdRaw;

        if ($this->commentRepository->hasAdministratorAlreadyCommented((int) $adminId, (int) $profileId)) {
            return response()->json([
                'message' => 'Profil déjà commenté.',
            ], 409);
        }

        $data = $request->validated();

        $data['administrator_id'] = $adminId;
        $data['content']          = $request->text;

        $comment = $this->createCommentAction->execute($data);

        return response()->json($comment, 201);
    }
}
