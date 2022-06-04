<?php

namespace App\Models;

class Tag extends AbstractModel
{
    protected $guarded = [];
    protected $hidden = ['uuid'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];

    public function lessons()
    {
        return $this->morphedByMany(Lesson::class, 'taggable');
    }

    public function courses()
    {
        return $this->morphedByMany(
            Course::class,
            'taggable',
            'taggables',
            null,
            null,
            null,
            'id'
        );
    }

    public function videos()
    {
        return $this->morphedByMany(Video::class, 'taggable');
    }

    public function tasks()
    {
        return $this->morphedByMany(Task::class, 'taggable');
    }
}
