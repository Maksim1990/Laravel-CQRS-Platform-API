<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CoursesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function not_auth_user_can_not_create_a_course()
    {
        $this
            ->withEnabledTokenAutentication()
            ->actingAs(Auth::loginUsingId(1))
            ->createCourse()
            ->assertUnauthorized();
    }

    /** @test */
    public function auth_user_can_create_a_course()
    {
        $this->createCourse()->assertCreated();
    }

    /** @test */
    public function auth_user_can_get_list_of_courses()
    {
        $firstCourse = $this->createCourse();
        $secondCourse = $this->createCourse([
            "name" => "Test course for Beginners SECOND",
            "description" => "Test course simple course SECOND",
            "slug" => "test-course-for-beginners-2"
        ]);

        $data = $this->getJson(self::COURSES_ENDPOINT);
        $this->assertCount(2, $data->decodeResponseJson()->json()['data']);

        $slugData = collect($data->decodeResponseJson()->json()['data'])->pluck('slug')->sort()->values();
        $this->assertEquals($firstCourse->getOriginalContent()->slug, $slugData->first());
        $this->assertEquals($secondCourse->getOriginalContent()->slug, $slugData->get(1));

        $data
            ->assertStatus(200)
            ->assertSee('courses')
            ->assertJsonFragment(['type' => 'courses'])
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
    public function auth_user_can_see_specific_course()
    {
        $this->getJson(self::COURSES_ENDPOINT . "/{$this->createCourse()->getOriginalContent()->slug}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'slug',
                    'description',
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Test course for Beginners',
                'description' => 'Test course simple course',
            ]);
    }

    /** @test */
    public function auth_user_can_update_a_course()
    {
        $this->patchJson(self::COURSES_ENDPOINT . "/{$this->createCourse()->getOriginalContent()->slug}", [
            "name" => "Test course for Beginners UPDATED",
            "description" => "Test course simple course UPDATED",
        ])->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'slug',
                    'description',
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Test course for Beginners UPDATED',
                'description' => 'Test course simple course UPDATED',
            ]);
    }

    /** @test */
    public function auth_user_can_delete_specific_course()
    {
        $course = $this->createCourse()->getOriginalContent();
        $this->getJson(self::COURSES_ENDPOINT . "/{$course->slug}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'slug',
                    'description',
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Test course for Beginners',
                'description' => 'Test course simple course',
            ]);

        $this->deleteJson(self::COURSES_ENDPOINT . "/{$course->slug}")
            ->assertStatus(200)->assertJsonFragment(['status' => 'Course deleted']);

        $this->getJson(self::COURSES_ENDPOINT . "/{$course->slug}")->assertNotFound();
    }
}
