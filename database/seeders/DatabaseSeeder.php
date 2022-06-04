<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\Task;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Unguard model
        Model::unguard();

        //USERS SEEDING
        if ($this->command->confirm('Do you want to seed Users with fake data ?', true)) {
            $numberOfUser = $this->command->ask('How many users do you need ?', 0);
            if (!empty($numberOfUser)) {
                $this->command->info("Creating {$numberOfUser} users, each will have a channel associated.");
                User::factory((int)$numberOfUser)->create();
                $this->command->line("Users table were successfully seeded");
            }
        }

        //COURSES SEEDING
        if ($this->command->confirm('Do you want to seed courses with fake data ?', true)) {
            $coursesNum = $this->command->ask('How many courses you want to create ?', '20');
            if (!empty($coursesNum)) {
                Course::factory((int)$coursesNum)->create();
                $this->command->line("Courses was successfully seeded");
            }
        }

        //SECTIONS SEEDING
        if ($this->command->confirm('Do you want to seed sections with fake data ?', true)) {
            $sectionsNum = $this->command->ask('How many sections you want to create ?', '20');
            if (!empty($sectionsNum)) {
                Section::factory((int)$sectionsNum)->create();
                $this->command->line("Sections was successfully seeded");
            }
        }

        //LESSONS SEEDING
        if ($this->command->confirm('Do you want to seed lessons with fake data ?', true)) {
            $lessonsNum = $this->command->ask('How many lessons you want to create ?', '20');
            if (!empty($lessonsNum)) {
                Lesson::factory((int)$lessonsNum)->create();
                $this->command->line("Lessons were successfully seeded");
            }
        }

        //COMMENTS SEEDING
        if ($this->command->confirm('Do you want to seed comments with fake data ?', true)) {
            $commentsNum = $this->command->ask('How many comments you want to create ?', '20');
            if (!empty($commentsNum)) {
                Comment::factory((int)$commentsNum)->create();
                $this->command->line("Comments were successfully seeded");
            }
        }

        //TASKS SEEDING
        if ($this->command->confirm('Do you want to seed tasks with fake data ?', true)) {
            $tasksNum = $this->command->ask('How many tasks you want to create ?', '20');
            if (!empty($tasksNum)) {
                Task::factory((int)$tasksNum)->create();
                $this->command->line("Tasks were successfully seeded");
            }
        }

        //VIDEOS SEEDING
        if ($this->command->confirm('Do you want to seed videos with fake data ?', true)) {
            $videosNum = $this->command->ask('How many videos you want to create ?', '20');
            if (!empty($videosNum)) {
                Video::factory((int)$videosNum)->create();
                $this->command->line("Videos were successfully seeded");
            }
        }

        // Re-guard model
        Model::reguard();
    }
}
