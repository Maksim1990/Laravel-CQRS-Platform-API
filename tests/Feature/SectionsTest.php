<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SectionsTest extends TestCase
{
    use RefreshDatabase;

    private const SECTIONS_ENDPOINT = '/api/v1/sections';

    public function setUp(): void
    {
        parent::setUp();
        $this->createCourse()->getOriginalContent();
    }

    /** @test */
    public function not_auth_user_can_not_create_a_section()
    {
        $this
            ->withEnabledTokenAutentication()
            ->actingAs(Auth::loginUsingId(1))
            ->createSection()
            ->assertUnauthorized();
    }

    /** @test */
    public function auth_user_can_create_a_section()
    {
        $this->createSection()->assertCreated();
    }

    /** @test */
    public function auth_user_can_get_list_of_sections()
    {
        $firstSection = $this->createSection();
        $this->getJson(self::SECTIONS_ENDPOINT . "/{$firstSection->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                "id" => $firstSection->getOriginalContent()->id,
                "name" => "Test section name",
                "description" => "Test section description"
            ]);

        $secondSection = $this->createSection([
            "name" => "Test section name SECOND",
            "description" => "Test section description SECOND",
            "course_id" => 1,
        ]);
        $this->getJson(self::SECTIONS_ENDPOINT . "/{$secondSection->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                "id" => $secondSection->getOriginalContent()->id,
                "name" => "Test section name SECOND",
                "description" => "Test section description SECOND",
            ]);

        $this->getJson(self::SECTIONS_ENDPOINT)
            ->assertStatus(200)
            ->assertSee('sections')
            ->assertJsonFragment(['type' => 'sections'])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                    ]
                ]
            ]);
    }

    /** @test */
    public function auth_user_can_see_specific_section()
    {
        $this->getJson(self::SECTIONS_ENDPOINT . "/{$this->createSection()->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'course_id',
                ]
            ])
            ->assertJsonFragment([
                "name" => "Test section name",
                "description" => "Test section description"
            ]);
    }

    /** @test */
    public function auth_user_can_update_a_section()
    {
        $this->patchJson(self::SECTIONS_ENDPOINT . "/{$this->createSection()->getOriginalContent()->id}", [
            "name" => "Test section name UPDATED",
            "description" => "Test section description UPDATED"
        ])->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'course_id',
                ]
            ])
            ->assertJsonFragment([
                "name" => "Test section name UPDATED",
                "description" => "Test section description UPDATED"
            ]);
    }

    /** @test */
    public function auth_user_can_delete_specific_section()
    {
        $section = $this->createSection()->getOriginalContent();
        $this->getJson(self::SECTIONS_ENDPOINT . "/{$section->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'course_id',
                ]
            ])
            ->assertJsonFragment([
                "name" => "Test section name",
                "description" => "Test section description"
            ]);

        $this->deleteJson(self::SECTIONS_ENDPOINT . "/{$section->id}")
            ->assertStatus(200)->assertJsonFragment(['status' => 'Section deleted']);

        $this->getJson(self::SECTIONS_ENDPOINT . "/{$section->id}")->assertNotFound();
    }

    protected function createSection($data = null)
    {
        return $this->postJson(self::SECTIONS_ENDPOINT, $data ?? [
                "name" => "Test section name",
                "course_id" => 1,
                "description" => "Test section description"
            ]);
    }
}
