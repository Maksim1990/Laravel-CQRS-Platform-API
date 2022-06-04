<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class Video extends AbstractModel
{
    use Searchable;

    protected $guarded = [];
    protected $hidden = ['uuid'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'lesson_id' => 'integer',
    ];

    public function searchableAs()
    {
        return 'videos';
    }

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'id' => $this->id,
            'description' => $this->description,
            'link' => $this->link,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

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
