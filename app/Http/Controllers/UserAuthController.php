<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    // Register a new user
    public function register(Request $request): JsonResponse
    {
        // Validate user registration data
        $validatedData = $this->validateRegistration($request);

        // Create a new user
        $user = $this->userRepository->createUser($validatedData);

        // Return response with the new user data
        return response()->json($user, JsonResponse::HTTP_CREATED);
    }

    // Authenticate user login
    public function login(Request $request): JsonResponse
    {
        // Validate user login credentials
        $credentials = $this->validateLogin($request);

        // Attempt user authentication
        $response = $this->userRepository->authenticate($credentials);

        // If authentication successful, return user data and token
        if ($response) {
            return response()->json($response);
        }

        // If authentication failed, return unauthorized error
        return response()->json(['message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    // Logout user
    public function logout(): JsonResponse
    {
        // Delete the current user's access token
        Auth::user()->currentAccessToken()->delete();

        // Return response confirming successful logout
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Validate user registration data
    private function validateRegistration(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
    }

    // Validate user login credentials
    private function validateLogin(Request $request): array
    {
        return $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    }
}
