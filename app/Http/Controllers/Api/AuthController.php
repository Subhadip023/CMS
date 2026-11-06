<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authenticate user and issue personal access token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password credentials.',
            ], 422);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->status === 'suspended') {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Your account has been suspended. Please contact support.',
            ], 403);
        }

        $token = $user->createToken('cms_auth_token')->plainTextToken;

        ActivityLog::record('user.login', null, $user, ['ip' => $request->ip()]);

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully.',
            'data' => [
                'user' => $user->load('roles'),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_super_admin' => false,
            'status' => 'active',
            'avatar_url' => 'https://ui-avatars.com/api/?name=' . urlencode($request->name) . '&background=2563eb&color=fff',
        ]);

        // Assign default subscriber role
        $subscriberRole = Role::where('slug', 'subscriber')->first();
        if ($subscriberRole) {
            $user->roles()->attach($subscriberRole->id);
        }

        $token = $user->createToken('cms_auth_token')->plainTextToken;

        ActivityLog::record('user.registered', null, $user);

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'data' => [
                'user' => $user->load('roles'),
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Get current authenticated user profile with roles and websites.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->load(['roles.permissions']),
                'websites' => $user->is_super_admin
                    ? \App\Models\Website::withCount(['pages', 'posts'])->get()
                    : $user->websites()->withCount(['pages', 'posts'])->get(),
            ],
        ]);
    }

    /**
     * Revoke user tokens on logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'string', 'url'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }
        if (isset($validated['avatar_url'])) {
            $user->avatar_url = $validated['avatar_url'];
        }
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $user,
        ]);
    }
}
