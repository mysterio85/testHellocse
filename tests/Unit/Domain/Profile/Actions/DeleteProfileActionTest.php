<?php

namespace Tests\Unit\Domain\Profile\Actions;

use App\Domain\Profile\Actions\DeleteProfileAction;
use App\Domain\Profile\Models\Profile;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class DeleteProfileActionTest extends TestCase
{
    protected DeleteProfileAction $deleteProfileAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteProfileAction = new DeleteProfileAction();
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[RunInSeparateProcess]
    public function testDeleteProfile(): void
    {
        /** @var MockInterface&Profile $profileMock */
        $profileMock = \Mockery::mock(Profile::class)->makePartial();

        /*
         * @phpstan-ignore-next-line
         */
        $profileMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $result = $this->deleteProfileAction->execute($profileMock);

        $this->assertTrue($result);
    }
}
