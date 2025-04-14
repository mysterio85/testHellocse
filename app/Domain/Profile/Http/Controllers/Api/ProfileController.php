<?php

namespace App\Domain\Profile\Http\Controllers\Api;

use App\Domain\Profile\Http\Requests\StoreProfileRequest;
use App\Domain\Profile\Http\Requests\UpdateProfileRequest;
use App\Domain\Profile\Http\Resources\ProfileResource;
use App\Domain\Profile\Models\Profile;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(): JsonResponse
    {
        $profiles = Profile::where('status', 'active')->get();

        return response()->json(ProfileResource::collection($profiles));
    }

    public function store(StoreProfileRequest $request): JsonResponse
    {
        $request->validated();
        $path = $request->file('image_path')->store('profiles', 'public');
        $profile = Profile::create([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'image_path' => $path,
            'status' => $request->status,
            'administrator_id' => auth()->id(),
        ]);

        return response()->json($profile, 201);
    }

    public function update(UpdateProfileRequest $request, int $profile): JsonResponse
    {
        $profile = Profile::findOrFail($profile);
        $validated = $request->validated();
        $validated['administrator_id'] = auth()->id();

        if ($request->hasFile('image_path')) {
            $image = $request->file('image_path')->store('profiles', 'public');
            $validated['image_path'] = $image;
        }
        $profile->update($validated);

        return response()->json([
            'message' => 'Profil mis à jour',
            'profile' => $profile
        ]);
    }

    public function destroy(Profile $profile): JsonResponse
    {
        if ($profile->image_path) {
            Storage::disk('public')->delete($profile->image_path);
        }

        $profile->delete();

        return response()->json(['message' => 'Le profil a été supprimé avec succès']);
    }
}
