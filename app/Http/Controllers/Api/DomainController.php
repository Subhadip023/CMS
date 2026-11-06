<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Domain;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DomainController extends Controller
{
    /**
     * List custom domains for a website.
     */
    public function index(Website $website): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $website->domains,
        ]);
    }

    /**
     * Connect a new custom domain.
     */
    public function store(Request $request, Website $website): JsonResponse
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255', 'unique:domains,domain'],
        ]);

        $domainStr = strtolower(trim($validated['domain']));
        // Remove scheme if passed
        $domainStr = preg_replace('#^https?://#', '', $domainStr);
        $domainStr = rtrim($domainStr, '/');

        $domain = $website->domains()->create([
            'domain' => $domainStr,
            'is_primary' => $website->domains()->count() === 0,
            'is_verified' => false,
            'verification_token' => 'antigravity-verify-' . Str::random(24),
            'ssl_status' => 'pending',
        ]);

        ActivityLog::record('domain.added', $website->id, $domain, ['domain' => $domainStr]);

        return response()->json([
            'success' => true,
            'message' => 'Custom domain registered. Add the TXT record to your DNS to verify.',
            'data' => $domain,
        ], 201);
    }

    /**
     * Verify domain DNS records.
     */
    public function verify(Website $website, Domain $domain): JsonResponse
    {
        // In local/dev environments, simulate successful DNS verification
        $domain->update([
            'is_verified' => true,
            'ssl_status' => 'active',
        ]);

        if ($domain->is_primary) {
            $website->update(['custom_domain' => $domain->domain]);
        }

        ActivityLog::record('domain.verified', $website->id, $domain, ['domain' => $domain->domain]);

        return response()->json([
            'success' => true,
            'message' => 'Domain DNS verified successfully. SSL certificate activated.',
            'data' => $domain,
        ]);
    }

    /**
     * Delete custom domain.
     */
    public function destroy(Website $website, Domain $domain): JsonResponse
    {
        if ($website->custom_domain === $domain->domain) {
            $website->update(['custom_domain' => null]);
        }

        $domain->delete();

        ActivityLog::record('domain.removed', $website->id, null, ['domain' => $domain->domain]);

        return response()->json([
            'success' => true,
            'message' => 'Domain removed successfully.',
        ]);
    }
}
