<?php

namespace Tests\Unit\Domain\Profile\Actions;

use App\Domain\Profile\Actions\UpdateProfileAction;
use App\Domain\Profile\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class UpdateProfileActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[RunInSeparateProcess] public function testUpdateProfile(): void
    {
        Storage::fake('public');

        $uploadedFile = UploadedFile::fake()->image('profile.jpg');

        $authMock = Mockery::mock();
        $authMock->shouldReceive('id')->andReturn(1);
        app()->instance('auth', $authMock);

        $profileMock = Mockery::mock(Profile::class);
        $profileMock->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['administrator_id'] === 1 &&
                    str_starts_with($data['image_path'], 'profiles/');
            }))
            ->andReturnTrue();

        $action = new UpdateProfileAction();

        $data = [
            'first_name' => 'Jane',
            'image_path' => $uploadedFile,
        ];

        $result = $action->execute($profileMock, $data);

        $this->assertSame($profileMock, $result);
    }
}
