<?php

namespace Tests\Feature\Api;

use App\Models\Course;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Full CRUD coverage for /api/courses.
 * Data providers test every validation rule and every role boundary.
 */
class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $consultant;
    private User $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->consultant = User::factory()->create();
        $this->consultant->assignRole('consultant');

        $this->client = User::factory()->create();
        $this->client->assignRole('client');
    }

    // ── Index ─────────────────────────────────────────────────

    public function test_admin_can_list_courses(): void
    {
        Course::factory()->count(3)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/courses')
            ->assertOk()
            ->assertJsonStructure(['data', 'meta']);
    }

    public function test_client_can_list_courses(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson('/api/courses')
            ->assertOk();
    }

    public function test_consultant_can_list_courses(): void
    {
        $this->actingAs($this->consultant, 'sanctum')
            ->getJson('/api/courses')
            ->assertOk();
    }

    public function test_unauthenticated_cannot_list_courses(): void
    {
        $this->getJson('/api/courses')->assertUnauthorized();
    }

    public function test_courses_index_returns_paginated_results(): void
    {
        Course::factory()->count(15)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/courses?per_page=5')
            ->assertOk();

        $this->assertCount(5, $response->json('data'));
    }

    public function test_courses_index_respects_per_page_param(): void
    {
        Course::factory()->count(20)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/courses?per_page=3')
            ->assertOk();

        $this->assertCount(3, $response->json('data'));
    }

    #[DataProvider('searchTermProvider')]
    public function test_courses_index_filters_by_search(string $searchTerm, int $expectedCount): void
    {
        Course::factory()->create(['name' => 'Laravel Fundamentals']);
        Course::factory()->create(['name' => 'Vue.js Advanced']);
        Course::factory()->create(['name' => 'Laravel Testing']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/courses?search={$searchTerm}")
            ->assertOk();

        $this->assertCount($expectedCount, $response->json('data'));
    }

    public static function searchTermProvider(): array
    {
        return [
            'exact match'       => ['Laravel Fundamentals', 1],
            'partial match'     => ['Laravel', 2],
            'no match'          => ['Python', 0],
        ];
    }

    // ── Show ──────────────────────────────────────────────────

    public function test_admin_can_view_single_course(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/courses/{$course->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $course->id);
    }

    public function test_client_can_view_single_course(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/courses/{$course->id}")
            ->assertOk();
    }

    public function test_show_returns_404_for_missing_course(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/courses/99999')
            ->assertNotFound();
    }

    public function test_unauthenticated_cannot_view_course(): void
    {
        $course = Course::factory()->create();
        $this->getJson("/api/courses/{$course->id}")->assertUnauthorized();
    }

    // ── Store ─────────────────────────────────────────────────

    public function test_admin_can_create_course(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/courses', [
                'name'        => 'New Course',
                'description' => 'A great course.',
            ])->assertCreated()
            ->assertJsonStructure(['message', 'course']);
    }

    public function test_consultant_can_create_course(): void
    {
        $this->actingAs($this->consultant, 'sanctum')
            ->postJson('/api/courses', ['name' => 'Consultant Course'])
            ->assertCreated();
    }

    public function test_client_cannot_create_course(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->postJson('/api/courses', ['name' => 'Hack Course'])
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_create_course(): void
    {
        $this->postJson('/api/courses', ['name' => 'Hack Course'])->assertUnauthorized();
    }

    public function test_course_creation_auto_generates_slug_from_name(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/courses', ['name' => 'Auto Slug Test'])
            ->assertCreated();

        $this->assertSame('auto-slug-test', $response->json('course.slug'));
    }

    public function test_course_creation_accepts_explicit_slug(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/courses', [
                'name' => 'My Course',
                'slug' => 'custom-slug',
            ])->assertCreated();

        $this->assertSame('custom-slug', $response->json('course.slug'));
    }

    public function test_course_persisted_to_database(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/courses', ['name' => 'DB Course']);

        $this->assertDatabaseHas('courses', ['name' => 'DB Course']);
    }

    #[DataProvider('invalidCourseStoreProvider')]
    public function test_course_creation_fails_validation(array $overrides): void
    {
        $valid = ['name' => 'Valid Course'];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/courses', array_merge($valid, $overrides))
            ->assertUnprocessable();
    }

    public static function invalidCourseStoreProvider(): array
    {
        return [
            'missing name'        => [['name' => '']],
            'name too long'       => [['name' => str_repeat('x', 256)]],
            'slug too long'       => [['name' => 'OK', 'slug' => str_repeat('s', 256)]],
        ];
    }

    public function test_duplicate_slug_is_rejected(): void
    {
        Course::factory()->create(['slug' => 'existing-slug']);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/courses', ['name' => 'Another', 'slug' => 'existing-slug'])
            ->assertUnprocessable();
    }

    // ── Update ────────────────────────────────────────────────

    public function test_admin_can_update_any_course(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/courses/{$course->id}", ['name' => 'Updated Name'])
            ->assertOk()
            ->assertJsonPath('course.name', 'Updated Name');
    }

    public function test_consultant_can_update_own_course(): void
    {
        $course = Course::factory()->create(['user_id' => $this->consultant->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->putJson("/api/courses/{$course->id}", ['name' => 'Updated by Consultant'])
            ->assertOk();
    }

    public function test_consultant_cannot_update_another_consultants_course(): void
    {
        $other = User::factory()->create();
        $other->assignRole('consultant');
        $course = Course::factory()->create(['user_id' => $other->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->putJson("/api/courses/{$course->id}", ['name' => 'Hijacked'])
            ->assertForbidden();
    }

    public function test_client_cannot_update_course(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/courses/{$course->id}", ['name' => 'Hack'])
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_update_course(): void
    {
        $course = Course::factory()->create();
        $this->putJson("/api/courses/{$course->id}", ['name' => 'Hack'])->assertUnauthorized();
    }

    public function test_patch_update_works_same_as_put(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/courses/{$course->id}", ['name' => 'Patched'])
            ->assertOk();
    }

    public function test_update_allows_same_slug_for_same_course(): void
    {
        $course = Course::factory()->create(['slug' => 'my-slug']);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/courses/{$course->id}", [
                'name' => 'Updated Name',
                'slug' => 'my-slug',
            ])->assertOk();
    }

    public function test_update_rejects_slug_already_used_by_another_course(): void
    {
        Course::factory()->create(['slug' => 'taken-slug']);
        $course = Course::factory()->create(['slug' => 'my-slug']);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/courses/{$course->id}", [
                'name' => 'Updated',
                'slug' => 'taken-slug',
            ])->assertUnprocessable();
    }

    #[DataProvider('invalidCourseUpdateProvider')]
    public function test_course_update_fails_validation(array $payload): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/courses/{$course->id}", $payload)
            ->assertUnprocessable();
    }

    public static function invalidCourseUpdateProvider(): array
    {
        return [
            'empty name'    => [['name' => '']],
            'name too long' => [['name' => str_repeat('x', 256)]],
            'slug too long' => [['name' => 'OK', 'slug' => str_repeat('s', 256)]],
        ];
    }

    // ── Destroy ───────────────────────────────────────────────

    public function test_admin_can_delete_course(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/courses/{$course->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Course deleted successfully');
    }

    public function test_consultant_can_delete_own_course(): void
    {
        $course = Course::factory()->create(['user_id' => $this->consultant->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->deleteJson("/api/courses/{$course->id}")
            ->assertOk();
    }

    public function test_consultant_cannot_delete_another_consultants_course(): void
    {
        $course = Course::factory()->create(['user_id' => $this->admin->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->deleteJson("/api/courses/{$course->id}")
            ->assertForbidden();
    }

    public function test_client_cannot_delete_course(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->client, 'sanctum')
            ->deleteJson("/api/courses/{$course->id}")
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_delete_course(): void
    {
        $course = Course::factory()->create();
        $this->deleteJson("/api/courses/{$course->id}")->assertUnauthorized();
    }

    public function test_delete_soft_deletes_the_course(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/courses/{$course->id}")
            ->assertOk();

        $this->assertSoftDeleted('courses', ['id' => $course->id]);
    }

    public function test_soft_deleted_course_not_in_index(): void
    {
        $course = Course::factory()->create();
        $course->delete();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/courses')
            ->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertNotContains($course->id, $ids);
    }

    public function test_delete_returns_404_for_missing_course(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson('/api/courses/99999')
            ->assertNotFound();
    }
}
