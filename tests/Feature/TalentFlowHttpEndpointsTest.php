<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TalentFlowHttpEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(DepartmentSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_all_core_endpoints(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $endpoints = [
            '/admin/dashboard',
            '/admin/clients',
            '/admin/leads',
            '/admin/activities',
            '/admin/celebrities',
            '/admin/categories',
            '/admin/quotations',
            '/admin/projects',
            '/admin/bookings',
            '/admin/calendar/events',
            '/admin/calendar/events?start=2026-11-01T00:00:00+05:30&end=2026-12-06T00:00:00+05:30',
            '/admin/calendar/events?start=2026-11-01T00:00:00%2B05:30&end=2026-12-06T00:00:00%2B05:30',
            '/admin/contracts',
            '/admin/documents',
            '/admin/claims',
            '/admin/invoices',
            '/admin/commissions',
            '/admin/reports/finance',
            '/admin/reports/projects',
            '/admin/reports/celebrities',
            '/admin/reports/quotations',
            '/admin/reports/claims',
            '/admin/reports/forecast',
            '/admin/settings',
        ];

        foreach ($endpoints as $url) {
            $response = $this->actingAs($user)->get($url);
            $response->assertStatus(200);
        }
    }
}

