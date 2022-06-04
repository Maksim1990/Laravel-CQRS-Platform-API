<?php

namespace App\Models;

class Task extends AbstractModel
{
    protected $guarded = [];
    protected $hidden = ['uuid'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'lesson_id' => 'integer',
    ];

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
