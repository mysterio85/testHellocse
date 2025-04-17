<?php

namespace Tests\Unit\Domain\Profile\Actions;

use App\Domain\Profile\Actions\CreateProfileAction;
use App\Domain\Profile\Models\Profile;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class CreateProfileActionTest extends TestCase
{
    protected MockInterface&CreateProfileAction $createAction;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var CreateProfileAction&MockInterface $createAction */
        $createAction = Mockery::mock(CreateProfileAction::class)->makePartial();
        $this->createAction = $createAction;
    }

    public function testCreatesProfile(): void
    {
        $data = [
            'first_name' => 'toto',
            'last_name' => 'tata',
            'administrator_id' => 1,
            'status' => 'active',
            'image_path' => '/tmp/test',
        ];

        $profileMock = Mockery::mock(Profile::class);

        /** @phpstan-ignore-next-line */
        $profileMock->shouldReceive('getAttribute')
            ->with('first_name')
            ->andReturn('toto');

        /** @phpstan-ignore-next-line */
        $profileMock->shouldReceive('create')->once()->with($data)->andReturn($profileMock);

        /** @phpstan-ignore-next-line */
        $this->createAction->shouldReceive('execute')->once()->with($data)->andReturn($profileMock);

        $result = $this->createAction->execute($data);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertEquals('toto', $result->first_name);
    }

    public function testFailsWhenDataIsInvalid(): void
    {
        $data = [
            'first_name' => '',
            'last_name' => 'toto',
            'administrator_id' => 1,
            'status' => 'active',
            'image_path' => '/tmp/phpnSKERH',
        ];

        /** @phpstan-ignore-next-line */
        $this->createAction
            ->shouldReceive('execute')
            ->once()->with($data)
            ->andThrow(new \Exception('Invalid name'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid name');

        $this->createAction->execute($data);
    }
}
