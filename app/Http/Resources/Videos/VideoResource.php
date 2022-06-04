<?php

namespace App\Http\Resources\Videos;

use App\Http\Resources\AbstractResource;

class VideoResource extends AbstractResource
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
                'title' => $this->title,
                'description' => $this->description,
                'link' => $this->link,
                'user_id' => $this->user_id,
                'lesson_id' => $this->lesson_id,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            $request
        );
    }
}
