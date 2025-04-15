<?php

namespace App\Domain\Profile\Services;

use App\Domain\Profile\Actions\CreateProfileAction;
use App\Domain\Profile\Actions\DeleteProfileAction;
use App\Domain\Profile\Actions\UpdateProfileAction;
use App\Domain\Profile\Http\Requests\UpdateProfileRequest;
use App\Domain\Profile\Http\Resources\ProfileResource;
use App\Domain\Profile\Models\Profile;
use App\Domain\Profile\Repositories\ProfileRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;

class ProfileService
{
    public function __construct(
        protected CreateProfileAction $createAction,
        protected UpdateProfileAction $updateAction,
        protected DeleteProfileAction $deleteAction,
        protected ProfileRepository $profileRepository,
    ) {
    }

    public function getAllActiveProfiles(): AnonymousResourceCollection
    {
        $profiles =  $this->profileRepository->getAllActive();

        return ProfileResource::collection($profiles);
    }

    /** @param array<string, mixed> $data */
    public function createProfile(array $data): Profile
    {
        if (isset($data['image_path'])) {
            $data['image_path'] = $this->storeImage($data['image_path']);
        }

        $data['administrator_id'] = auth()->id();

        return $this->createAction->execute($data);

    }
    public function updateProfile(UpdateProfileRequest $request, int $profileId): Profile
    {
        $profile = $this->profileRepository->getById($profileId);
        $validated = $request->validated();

        return $this->updateAction->execute($profile, $validated);
    }

    public function delete(int $profileId): ?bool
    {
        $profile = Profile::findOrFail($profileId);

        return $this->deleteAction->execute($profile);
    }

    protected function storeImage(UploadedFile $image): false|string
    {
        return $image->store('profiles', 'public');
    }

}
