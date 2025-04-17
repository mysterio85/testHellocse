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
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class ProfileServiceTest extends TestCase
{
    protected ProfileService $profileService;

    protected CreateProfileAction&MockInterface $createAction;

    protected UpdateProfileAction&MockInterface $updateAction;

    protected DeleteProfileAction&MockInterface $deleteAction;

    protected ProfileRepository&MockInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var  CreateProfileAction&MockInterface $createAction */
        $createAction = Mockery::mock(CreateProfileAction::class);
        $this->createAction = $createAction;

        /** @var  UpdateProfileAction&MockInterface $updateAction */
        $updateAction = Mockery::mock(UpdateProfileAction::class);
        $this->updateAction = $updateAction;

        /** @var  DeleteProfileAction&MockInterface $deleteAction */
        $deleteAction = Mockery::mock(DeleteProfileAction::class);
        $this->deleteAction = $deleteAction;

        /** @var  ProfileRepository&MockInterface $repository */
        $repository = Mockery::mock(ProfileRepository::class);
        $this->repository = $repository;

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

        /** @var  ProfileService&MockInterface $service */
        $service = Mockery::mock(ProfileService::class, [
            $this->createAction, $this->updateAction, $this->deleteAction, $this->repository
        ])->makePartial();

        /** @phpstan-ignore-next-line */
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

        /** @var UpdateProfileRequest&MockInterface $request */
        $request = Mockery::mock(UpdateProfileRequest::class);

        /** @phpstan-ignore-next-line */
        $request->shouldReceive('validated')
            ->andReturn(['first_name' => 'toto']);

        /** @phpstan-ignore-next-line */
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

        /** @phpstan-ignore-next-line */
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

        /** @phpstan-ignore-next-line */
        $this->repository
            ->shouldReceive('getAllActive')
            ->once()
            ->andReturn($activeProfiles);

        $result = $this->profileService->getAllActiveProfiles();

        /** @var array<int, array<string, mixed>> $resultArray */
        $resultArray = $result->toArray(request());

        $this->assertInstanceOf(AnonymousResourceCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('toto', $resultArray[0]['first_name']);
    }

}
