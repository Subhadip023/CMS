<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\StoreWebsiteRequest;
use App\Http\Requests\Website\UpdateWebsiteRequest;
use App\Models\ActivityLog;
use App\Models\Website;
use App\Services\WebsiteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function __construct(
        protected WebsiteService $websiteService
    ) {}

    /**
     * List all websites for the current user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = $user->is_super_admin
            ? Website::query()
            : Website::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('owner', fn($sq) => $sq->where('id', $user->id));
            });

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subdomain', 'like', "%{$search}%")
                  ->orWhere('custom_domain', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $websites = $query->with(['themeSettings.theme', 'domains'])
            ->withCount(['pages', 'posts', 'media', 'formSubmissions'])
            ->orderByDesc('updated_at')
            ->paginate($request->get('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => $websites,
        ]);
    }

    /**
     * Store a newly created website.
     */
    public function store(StoreWebsiteRequest $request): JsonResponse
    {
        $website = $this->websiteService->createWebsite(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Website created successfully.',
            'data' => $website,
        ], 201);
    }

    /**
     * Display the specified website.
     */
    public function show(Request $request, Website $website): JsonResponse
    {
        $website->load([
            'owner',
            'domains',
            'themeSettings.theme',
            'homePage',
        ])->loadCount(['pages', 'posts', 'media', 'formSubmissions']);

        return response()->json([
            'success' => true,
            'data' => $website,
        ]);
    }

    /**
     * Update the specified website.
     */
    public function update(UpdateWebsiteRequest $request, Website $website): JsonResponse
    {
        $data = $request->validated();
        
        if (isset($data['settings']) && is_array($data['settings'])) {
            $data['settings'] = array_merge($website->settings ?? [], $data['settings']);
        }

        $website->update($data);

        ActivityLog::record('website.updated', $website->id, $website);

        return response()->json([
            'success' => true,
            'message' => 'Website settings updated successfully.',
            'data' => $website->fresh(['domains', 'themeSettings.theme']),
        ]);
    }

    /**
     * Remove the specified website.
     */
    public function destroy(Request $request, Website $website): JsonResponse
    {
        $websiteId = $website->id;
        $websiteName = $website->name;

        $website->delete();

        ActivityLog::record('website.deleted', $websiteId, null, ['name' => $websiteName]);

        return response()->json([
            'success' => true,
            'message' => 'Website deleted successfully.',
        ]);
    }

    /**
     * Publish website (make publicly viewable).
     */
    public function publish(Request $request, Website $website): JsonResponse
    {
        $website->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        ActivityLog::record('website.published', $website->id, $website);

        return response()->json([
            'success' => true,
            'message' => 'Website is now live and published!',
            'data' => [
                'is_published' => true,
                'published_at' => $website->published_at,
                'live_url' => $website->live_url,
            ],
        ]);
    }

    /**
     * Unpublish website.
     */
    public function unpublish(Request $request, Website $website): JsonResponse
    {
        $website->update([
            'is_published' => false,
        ]);

        ActivityLog::record('website.unpublished', $website->id, $website);

        return response()->json([
            'success' => true,
            'message' => 'Website has been unpublished and taken offline.',
            'data' => [
                'is_published' => false,
            ],
        ]);
    }

    /**
     * Duplicate a website.
     */
    public function duplicate(Request $request, Website $website): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'subdomain' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:websites,subdomain'],
        ]);

        $newWebsite = $this->websiteService->duplicateWebsite(
            $website,
            $request->user(),
            $request->name,
            $request->subdomain
        );

        return response()->json([
            'success' => true,
            'message' => 'Website cloned successfully.',
            'data' => $newWebsite,
        ], 201);
    }
}
