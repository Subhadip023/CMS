<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use Tests\TestCase;

class WebsiteManagementTest extends TestCase
{
    public function test_user_can_create_website(): void
    {
        $user = User::factory()->create();
        $subdomain = 'site-' . time();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/websites', [
                'name' => 'Tech Innovators',
                'subdomain' => $subdomain,
                'category' => 'business',
                'description' => 'A showcase for innovation',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Tech Innovators')
            ->assertJsonPath('data.subdomain', $subdomain);

        $this->assertDatabaseHas('websites', [
            'subdomain' => $subdomain,
            'user_id' => $user->id,
        ]);

        $website = Website::where('subdomain', $subdomain)->first();
        $this->assertNotNull($website);
        $this->assertDatabaseHas('pages', [
            'website_id' => $website->id,
            'is_homepage' => true,
        ]);
    }

    public function test_user_can_publish_and_unpublish_website(): void
    {
        $user = User::factory()->create();
        $website = Website::create([
            'user_id' => $user->id,
            'name' => 'Publish Test Site',
            'subdomain' => 'pub-' . time(),
            'is_published' => false,
        ]);

        // Publish
        $publishResponse = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/websites/{$website->id}/publish");

        $publishResponse->assertStatus(200)
            ->assertJsonPath('data.is_published', true);

        $this->assertTrue($website->fresh()->is_published);

        // Unpublish
        $unpublishResponse = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/websites/{$website->id}/unpublish");

        $unpublishResponse->assertStatus(200)
            ->assertJsonPath('data.is_published', false);

        $this->assertFalse($website->fresh()->is_published);
    }

    public function test_user_can_duplicate_website(): void
    {
        $user = User::factory()->create();
        $website = Website::create([
            'user_id' => $user->id,
            'name' => 'Original Site',
            'subdomain' => 'orig-' . time(),
            'is_published' => false,
        ]);

        $newSubdomain = 'clone-' . time();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/websites/{$website->id}/duplicate", [
                'name' => 'Cloned Site',
                'subdomain' => $newSubdomain,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Cloned Site');

        $this->assertDatabaseHas('websites', [
            'subdomain' => $newSubdomain,
            'user_id' => $user->id,
        ]);
    }

    public function test_tenant_isolation_prevents_unauthorized_access(): void
    {
        $owner = User::factory()->create(['is_super_admin' => false]);
        $intruder = User::factory()->create(['is_super_admin' => false]);

        $website = Website::create([
            'user_id' => $owner->id,
            'name' => 'Private Site',
            'subdomain' => 'priv-' . time(),
        ]);

        $response = $this->actingAs($intruder, 'sanctum')
            ->getJson("/api/v1/websites/{$website->id}");

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }
}
