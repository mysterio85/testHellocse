<?php

namespace Tests\Unit\Domain\Profile\Actions;

use App\Domain\Profile\Actions\CreateProfileAction;
use App\Domain\Profile\Models\Profile;
use Mockery;
use PHPUnit\Framework\TestCase;

class CreateProfileActionTest extends TestCase
{
    protected $createAction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createAction = Mockery::mock(CreateProfileAction::class)->makePartial();
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

        $profileMock->shouldReceive('getAttribute')
            ->with('first_name')
            ->andReturn('toto');

        $profileMock->shouldReceive('create')->once()->with($data)->andReturn($profileMock);

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

        $this->createAction
            ->shouldReceive('execute')
            ->once()->with($data)
            ->andThrow(new \Exception('Invalid name'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid name');

        $this->createAction->execute($data);
    }
}
