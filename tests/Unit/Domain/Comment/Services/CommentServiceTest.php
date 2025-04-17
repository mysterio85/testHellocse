<?php

namespace Tests\Unit\Domain\Comment\Services;

use App\Domain\Comment\Actions\CreateCommentAction;
use App\Domain\Comment\Http\Requests\StoreCommentRequest;
use App\Domain\Comment\Models\Comment;
use App\Domain\Comment\Repositories\CommentRepository;
use App\Domain\Comment\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class CommentServiceTest extends TestCase
{
    protected CommentService $commentService;
    protected (Mockery\MockInterface&Mockery\LegacyMockInterface)|CreateCommentAction $createCommentAction;
    protected CommentRepository|(Mockery\MockInterface&Mockery\LegacyMockInterface) $commentRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createCommentAction = Mockery::mock(CreateCommentAction::class);
        $this->commentRepository = Mockery::mock(CommentRepository::class);

        $this->commentService = new CommentService(
            $this->createCommentAction,
            $this->commentRepository
        );
    }

    public function testCreateCommentWhenNotAlreadyCommented()
    {
        $request = Mockery::mock(StoreCommentRequest::class);
        $adminId = 1;
        $profileId = 10;
        $validatedData = ['profile_id' => $profileId];

        $this->authenticateAsAdministrator($adminId);

        $request->shouldReceive('input')->with('profile_id')->andReturn($profileId);
        $request->shouldReceive('validated')->andReturn($validatedData);
        $request->text = 'comment test';

        $this->commentRepository
            ->shouldReceive('hasAdministratorAlreadyCommented')
            ->once()
            ->with($adminId, $profileId)
            ->andReturn(false);

        $expectedComment = new Comment(['content' => 'comment test']);

        $this->createCommentAction
            ->shouldReceive('execute')
            ->once()
            ->with(Mockery::on(function ($data) use ($adminId, $profileId) {
                return $data['administrator_id'] === $adminId &&
                    $data['profile_id'] === $profileId &&
                    $data['content'] === 'comment test';
            }))
            ->andReturn($expectedComment);

        $response = $this->commentService->createComment($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($expectedComment->toArray(), $response->getData(true));
    }

    public function testCreateCommentWhenAlreadyCommented()
    {
        $request = Mockery::mock(StoreCommentRequest::class);
        $adminId = 1;
        $profileId = 10;

        $this->authenticateAsAdministrator($adminId);

        $request->shouldReceive('input')->with('profile_id')->andReturn($profileId);

        $this->commentRepository
            ->shouldReceive('hasAdministratorAlreadyCommented')
            ->once()
            ->with($adminId, $profileId)
            ->andReturn(true);

        $response = $this->commentService->createComment($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals(['message' => 'Profil déjà commenté.'], $response->getData(true));
    }

    protected function authenticateAsAdministrator(int $id): void
    {
        Auth::shouldReceive('id')->andReturn($id);
    }
}
