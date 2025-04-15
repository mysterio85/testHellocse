<?php

namespace App\Domain\Profile\Http\Controllers\Api;

use App\Domain\Profile\Http\Requests\StoreProfileRequest;
use App\Domain\Profile\Http\Requests\UpdateProfileRequest;
use App\Domain\Profile\Services\ProfileService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $profileService)
    {
    }

    public function index(): JsonResponse
    {
        $profiles = $this->profileService->getAllActiveProfiles();

        return response()->json($profiles);
    }

    public function store(StoreProfileRequest $request): JsonResponse
    {
        $profile = $this->profileService->createProfile($request->validated());

        return response()->json($profile, 201);
    }

    public function update(UpdateProfileRequest $request, int $profileId): JsonResponse
    {
        $profile = $this->profileService->updateProfile($request, $profileId);

        return response()->json([
            'message' => 'Profil mis à jour',
            'profile' => $profile
        ], 201);
    }

    public function destroy(int $profileId): JsonResponse
    {
        $this->profileService->delete($profileId);

        return response()->json(['message' => 'Le profil a été supprimé avec succès']);
    }
}
