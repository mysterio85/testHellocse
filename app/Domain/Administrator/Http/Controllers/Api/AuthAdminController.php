<?php

namespace App\Domain\Administrator\Http\Controllers\Api;

use App\Domain\Administrator\Models\Administrator;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthAdminController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        /** @var string $email */
        $email = $request->email;

        /** @var string $password */
        $password = $request->password;

        $admin = Administrator::where('email', $email)->first();

        if (!$admin || !Hash::check($password, $admin->password)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        $admin->tokens()->delete();

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'token'         => $token,
            'administrator' => $admin,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var Administrator|null $administrator */
        $administrator = $request->user();

        if ($administrator?->currentAccessToken()) {
            $administrator->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Déconnexion réussie.'], 200);
    }
}
