<?php
namespace App\Models;

use App\Mappings\ModelMapping;

class Comment extends AbstractModel
{
    protected $guarded = [];
    protected $hidden = ['uuid'];
    protected $with = ['user'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'commentable_id' => 'integer',
    ];

    /**
     * Get the owning commentable model.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCommentableTypeAttribute($value): string
    {
        return ModelMapping::MODELS_LIST[$value];
    }
}
