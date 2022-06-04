<?php
namespace App\Http\Resources\Comments;

use App\Http\Resources\AbstractResource;

class CommentResource extends AbstractResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->buildDataStructure(
            [
                'id' => $this->id,
                'message' => $this->message,
                'type' => $this->commentable_type,
                'model_id' => $this->commentable_id,
                'user_id' => $this->user_id,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            $request
        );
    }
}
