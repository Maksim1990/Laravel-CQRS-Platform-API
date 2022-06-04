<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected const COURSES_ENDPOINT = '/api/v1/courses';
    protected const LESSONS_ENDPOINT = '/api/v1/lessons';

    public function setUp(): void
    {
        parent::setUp();

        $this
            ->withDisabledTokenAutentication()
            ->actingAs(User::factory(1)->createOne());
    }

    protected function withEnabledTokenAutentication()
    {
        config(['system.disable_test_auth_via_token' => false]);
        return $this;
    }

    protected function withDisabledTokenAutentication()
    {
        config(['system.disable_test_auth_via_token' => true]);
        return $this;
    }

    protected function createCourse($data = null)
    {
        return $this->postJson(self::COURSES_ENDPOINT, $data ?? [
                "name" => "Test course for Beginners",
                "description" => "Test course simple course",
                "slug" => "test-course-for-beginners"
            ]);
    }

    protected function createLesson($data = null)
    {
        return $this->postJson(self::LESSONS_ENDPOINT, $data ?? [
                "name" => "Test lesson name",
                "description" => "Test lesson description",
                "course_id" => 1
            ]);
    }
}
