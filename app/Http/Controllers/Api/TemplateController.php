<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\Theme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * List all pre-designed templates categorized.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Template::query();

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $templates = $query->orderBy('name')->get();

        $categories = [
            'all' => 'All Templates',
            'business' => 'Business & Enterprise',
            'portfolio' => 'Portfolio & Creative',
            'agency' => 'Digital Agency',
            'ecommerce' => 'E-Commerce & Store',
            'blog' => 'Blog & Magazine',
            'corporate' => 'Corporate Group',
            'landing' => 'SaaS & Landing Page',
            'personal' => 'Personal & Creator',
            'education' => 'Education & Academy',
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'templates' => $templates,
                'categories' => $categories,
            ],
        ]);
    }

    /**
     * List all available themes.
     */
    public function themes(): JsonResponse
    {
        $themes = Theme::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $themes,
        ]);
    }
}
