<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LessonsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->createCourse()->getOriginalContent();
    }

    /** @test */
    public function not_auth_user_can_not_create_a_lesson()
    {
        $this
            ->withEnabledTokenAutentication()
            ->actingAs(Auth::loginUsingId(1))
            ->createLesson()
            ->assertUnauthorized();
    }

    /** @test */
    public function auth_user_can_create_a_lesson()
    {
        $this->createLesson()->assertCreated();
    }

    /** @test */
    public function auth_user_can_get_list_of_lessons()
    {
        $firstLesson = $this->createLesson();
        $secondLesson = $this->createLesson([
            "name" => "Test lesson name SECOND",
            "description" => "Test lesson description SECOND",
            "course_id" => 1
        ]);

        $data = $this->getJson(self::LESSONS_ENDPOINT);
        $this->assertCount(2, $data->decodeResponseJson()->json()['data']);

        $nameData = collect($data->decodeResponseJson()->json()['data'])
            ->pluck('attributes.name')->sort()->values();
        $this->assertEquals($firstLesson->getOriginalContent()->name, $nameData->first());
        $this->assertEquals($secondLesson->getOriginalContent()->name, $nameData->get(1));

        $data
            ->assertStatus(200)
            ->assertSee('lessons')
            ->assertJsonFragment(['type' => 'lessons'])
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
    public function auth_user_can_see_specific_lesson()
    {
        $this->getJson(self::LESSONS_ENDPOINT . "/{$this->createLesson()->getOriginalContent()->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'course_id',
                    'description',
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Test lesson name',
                'description' => 'Test lesson description',
            ]);
    }

    /** @test */
    public function auth_user_can_update_a_lesson()
    {
        $this->patchJson(self::LESSONS_ENDPOINT . "/{$this->createLesson()->getOriginalContent()->id}", [
            'name' => 'Test lesson name UPDATED',
            'description' => 'Test lesson description UPDATED',
        ])->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'course_id',
                    'description',
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Test lesson name UPDATED',
                'description' => 'Test lesson description UPDATED',
            ]);
    }

    /** @test */
    public function auth_user_can_delete_specific_lesson()
    {
        $lesson = $this->createLesson()->getOriginalContent();
        $this->getJson(self::LESSONS_ENDPOINT . "/{$lesson->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'course_id',
                    'description',
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Test lesson name',
                'description' => 'Test lesson description',
            ]);

        $this->deleteJson(self::LESSONS_ENDPOINT . "/{$lesson->id}")
            ->assertStatus(200)->assertJsonFragment(['status' => 'Lesson deleted']);

        $this->getJson(self::LESSONS_ENDPOINT . "/{$lesson->id}")->assertNotFound();
    }
}
