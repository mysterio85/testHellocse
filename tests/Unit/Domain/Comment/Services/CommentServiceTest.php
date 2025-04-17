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
use Mockery\MockInterface;
use Tests\TestCase;

class CommentServiceTest extends TestCase
{
    protected CommentService $commentService;
    protected CreateCommentAction&MockInterface $createCommentAction;
    protected CommentRepository&MockInterface $commentRepository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var CreateCommentAction&MockInterface $createCommentAction */
        $createCommentAction = Mockery::mock(CreateCommentAction::class);
        $this->createCommentAction = $createCommentAction;

        /** @var CommentRepository&MockInterface $commentRepository */
        $commentRepository = Mockery::mock(CommentRepository::class);
        $this->commentRepository = $commentRepository;

        $this->commentService = new CommentService(
            $this->createCommentAction,
            $this->commentRepository
        );
    }

    public function testCreateCommentWhenNotAlreadyCommented(): void
    {
        /** @var StoreCommentRequest&MockInterface $request */
        $request = Mockery::mock(StoreCommentRequest::class);
        $adminId = 1;
        $profileId = 10;
        $validatedData = ['profile_id' => $profileId];

        $this->authenticateAsAdministrator($adminId);

        /** @phpstan-ignore-next-line */
        $request->shouldReceive('input')->with('profile_id')->andReturn($profileId);
        $request->shouldReceive('validated')->andReturn($validatedData);
        $request->text = 'comment test';

        /** @phpstan-ignore-next-line */
        $this->commentRepository
            ->shouldReceive('hasAdministratorAlreadyCommented')
            ->once()
            ->with($adminId, $profileId)
            ->andReturn(false);

        $expectedComment = new Comment(['content' => 'comment test']);

        /** @phpstan-ignore-next-line */
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

    public function testCreateCommentWhenAlreadyCommented(): void
    {
        /** @var StoreCommentRequest&MockInterface $request */
        $request = Mockery::mock(StoreCommentRequest::class);
        $adminId = 1;
        $profileId = 10;

        $this->authenticateAsAdministrator($adminId);

        /** @phpstan-ignore-next-line */
        $request->shouldReceive('input')->with('profile_id')->andReturn($profileId);

        /** @phpstan-ignore-next-line */
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
