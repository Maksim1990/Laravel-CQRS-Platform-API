<?php

namespace App\Http\Resources\Lessons;

use App\Http\Resources\AbstractResource;

class LessonResource extends AbstractResource
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
                'name' => $this->name,
                'description' => $this->description,
                'course_id' => (int) $this->course_id,
                'user_id' => (int) $this->user_id,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            $request
        );
    }
}
