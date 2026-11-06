<?php

namespace App\Http\Middleware;

use App\Models\Website;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Determine website id from route param, header, or query
        $websiteId = $request->route('website') ?? 
                     $request->route('websiteId') ?? 
                     $request->header('X-Website-Id') ?? 
                     $request->query('website_id');

        if ($websiteId instanceof Website) {
            $website = $websiteId;
            $websiteId = $website->id;
        } elseif ($websiteId) {
            $website = Website::withoutGlobalScopes()->find($websiteId);
        } else {
            return $next($request);
        }

        if (!$website) {
            return response()->json([
                'success' => false,
                'message' => 'Requested website was not found.'
            ], 404);
        }

        // 2. Super admins bypass ownership check
        if ($user && $user->is_super_admin) {
            app()->instance('current_website_id', $website->id);
            app()->instance('current_website', $website);
            return $next($request);
        }

        // 3. Check ownership or assigned role for this website
        if ($user) {
            $isOwner = (int)$website->user_id === (int)$user->id;
            $hasRole = $user->roles()
                ->wherePivot('website_id', $website->id)
                ->exists();

            if (!$isOwner && !$hasRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You do not have permission to access this website.'
                ], 403);
            }
        }

        // Bind active tenant
        app()->instance('current_website_id', $website->id);
        app()->instance('current_website', $website);

        return $next($request);
    }
}
