<?php
namespace App\Models;

use Laravel\Scout\Searchable;

class Lesson extends AbstractModel
{
    use Searchable;

    protected $guarded = [];
    protected $hidden = ['uuid'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'course_id' => 'integer',
        'section_id' => 'integer',
    ];

    public function searchableAs()
    {
        return 'lessons';
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
