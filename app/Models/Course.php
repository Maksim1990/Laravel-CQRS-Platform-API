<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class Course extends AbstractModel
{
    use Searchable;

    protected $primaryKey = 'slug';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];

    protected $guarded = [];

    protected $hidden = ['uuid'];

    public function searchableAs()
    {
        return 'courses';
    }

    public function toSearchableArray()
    {
        return [
            'slug' => $this->slug,
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    //    public function getScoutKey()
    //    {
    //        return 'slug';
    //    }

    //    public function getScoutKeyName()
    //    {
    //        return $this->slug;
    //    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'course_id', 'id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'course_id', 'id');
    }

    public function tags()
    {
        return $this->morphToMany(
            Tag::class,
            'taggable',
            'taggables',
            null,
            null,
            'id'
        );
    }

    public function comments()
    {
        return $this->morphMany(
            'App\Models\Comment',
            'commentable',
            null,
            'commentable_id',
            'id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
