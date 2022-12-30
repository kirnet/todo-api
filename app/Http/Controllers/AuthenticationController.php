<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\SignInRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Auth
{
    public function createAccount(CreateUserRequest $request): JsonResponse
    {
        $attr = $request->validated();

        $user = User::create([
            'name'     => $attr['name'],
            'password' => Hash::make($attr['password']),
            'email'    => $attr['email']
        ]);

        return response()->json([
            'token' => $user->createToken('tokens')->plainTextToken
        ]);
    }

    public function signIn(SignInRequest $request): JsonResponse
    {
        $attr = $request->validated();
        if ( ! Auth::attempt($attr)) {
            return response()->json(['Credentials not match'], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]);
    }

    public function checkToken(Request $request): JsonResponse
    {
        return response()->json([]);
    }

    // this method signs out users by removing tokens
    public function signOut(): array
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }
}
