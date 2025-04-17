<?php

namespace Tests\Unit\Domain\Profile\Services;

use App\Domain\Profile\Actions\CreateProfileAction;
use App\Domain\Profile\Actions\DeleteProfileAction;
use App\Domain\Profile\Actions\UpdateProfileAction;
use App\Domain\Profile\Http\Requests\UpdateProfileRequest;
use App\Domain\Profile\Models\Profile;
use App\Domain\Profile\Repositories\ProfileRepository;
use App\Domain\Profile\Services\ProfileService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class ProfileServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProfileService $profileService;

    /**
     * @var CreateProfileAction|MockInterface
     */
    protected CreateProfileAction|MockInterface $createAction;

    /**
     * @var UpdateProfileAction|MockInterface
     */
    protected UpdateProfileAction|MockInterface $updateAction;

    /**
     * @var DeleteProfileAction|MockInterface
     */
    protected DeleteProfileAction|MockInterface $deleteAction;

    /**
     * @var ProfileRepository|MockInterface
     */
    protected ProfileRepository|MockInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createAction = Mockery::mock(CreateProfileAction::class);
        $this->updateAction = Mockery::mock(UpdateProfileAction::class);
        $this->deleteAction = Mockery::mock(DeleteProfileAction::class);
        $this->repository = Mockery::mock(ProfileRepository::class);

        $this->profileService = new ProfileService(
            $this->createAction,
            $this->updateAction,
            $this->deleteAction,
            $this->repository
        );
    }

    #[RunInSeparateProcess] public function testCreateProfile(): void
    {
        $fakeImage = UploadedFile::fake()->image('profile.jpg');
        $data = [
            'first_name' => 'toto',
            'last_name' => 'tata',
            'image_path' => $fakeImage,
        ];

        $storedPath = 'profiles/profile.jpg';
        $expectedProfile = new Profile($data);
        $expectedProfile->image_path = $storedPath;

        Auth::shouldReceive('id')->once()->andReturn(1);

        /** @phpstan-ignore-next-line */
        $this->createAction
            ->shouldReceive('execute')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturn($expectedProfile);

        $service = Mockery::mock(ProfileService::class, [
            $this->createAction, $this->updateAction, $this->deleteAction, $this->repository
        ])->makePartial();

        $service->shouldAllowMockingProtectedMethods()
            ->shouldReceive('storeImage')
            ->once()
            ->with($fakeImage)
            ->andReturn($storedPath);

        $profile = $service->createProfile($data);

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals($storedPath, $profile->image_path);
    }


    #[RunInSeparateProcess] public function testUpdateProfileCallsAction(): void
    {
        $mockProfile = new Profile();
        $request = Mockery::mock(UpdateProfileRequest::class);
        $request->shouldReceive('validated')->andReturn(['first_name' => 'toto']);

        $this->repository
            ->shouldReceive('getById')
            ->with(1)
            ->andReturn($mockProfile);

        /** @phpstan-ignore-next-line */
        $this->updateAction
            ->shouldReceive('execute')
            ->with($mockProfile, ['first_name' => 'toto'])
            ->andReturn($mockProfile);

        $result = $this->profileService->updateProfile($request, 1);

        $this->assertEquals($mockProfile, $result);
    }

    #[RunInSeparateProcess] public function testDeleteProfile(): void
    {
        $profile = new Profile(['id' => 1, 'first_name' => 'Test', 'last_name' => 'User']);

        $this->repository
            ->shouldReceive('getById')
            ->with(1)
            ->andReturn($profile);

        /** @phpstan-ignore-next-line */
        $this->deleteAction->shouldReceive('execute')
            ->with($profile)
            ->once()
            ->andReturn(true);

        $result = $this->profileService->delete(1);

        $this->assertTrue($result);
    }


    #[RunInSeparateProcess] public function testGetAllActiveProfiles(): void
    {
        $profiles = new EloquentCollection([
            new Profile(['first_name' => 'toto', 'status' => 'active']),
            new Profile(['first_name' => 'tata', 'status' => 'active']),
            new Profile(['first_name' => 'koko', 'status' => 'inactive']),
        ]);

        $activeProfiles = $profiles->filter(fn ($profile) => $profile->status === 'active')->values();

        $this->repository
            ->shouldReceive('getAllActive')
            ->once()
            ->andReturn($activeProfiles);

        $result = $this->profileService->getAllActiveProfiles();
        $resultArray = $result->toArray(request());

        $this->assertInstanceOf(AnonymousResourceCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('toto', $resultArray[0]['first_name']);
    }

}
