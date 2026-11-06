<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\FormSubmission;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get aggregate statistics for the dashboard.
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $websiteId = $request->get('website_id');

        if ($websiteId) {
            $website = Website::findOrFail($websiteId);
            
            $stats = [
                'total_pages' => Page::where('website_id', $website->id)->count(),
                'published_pages' => Page::where('website_id', $website->id)->where('status', 'published')->count(),
                'total_posts' => Post::where('website_id', $website->id)->count(),
                'total_media' => Media::where('website_id', $website->id)->count(),
                'total_submissions' => FormSubmission::where('website_id', $website->id)->count(),
                'unread_submissions' => FormSubmission::where('website_id', $website->id)->where('is_read', false)->count(),
                'is_published' => $website->is_published,
                'live_url' => $website->live_url,
            ];

            $recentLogs = ActivityLog::where('website_id', $website->id)
                ->with('user')
                ->latest()
                ->take(10)
                ->get();
        } else {
            $sitesQuery = $user->is_super_admin
                ? Website::query()
                : Website::where('user_id', $user->id);

            $stats = [
                'total_websites' => $sitesQuery->count(),
                'published_websites' => (clone $sitesQuery)->where('is_published', true)->count(),
                'total_users' => $user->is_super_admin ? User::count() : 1,
                'total_pages' => Page::count(),
                'total_posts' => Post::count(),
                'total_media' => Media::count(),
                'storage_used_bytes' => Media::sum('file_size') ?: 0,
            ];

            $recentLogs = ActivityLog::with(['user', 'website'])
                ->latest()
                ->take(10)
                ->get();
        }

        $recentWebsites = ($user->is_super_admin ? Website::query() : Website::where('user_id', $user->id))
            ->withCount(['pages', 'posts'])
            ->latest('updated_at')
            ->take(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_logs' => $recentLogs,
                'recent_websites' => $recentWebsites,
            ],
        ]);
    }
}
