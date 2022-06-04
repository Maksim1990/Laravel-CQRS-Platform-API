<?php

namespace App\Http\Resources\Courses;

use App\Http\Resources\AbstractResource;

class CourseResource extends AbstractResource
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
                'slug' => $this->slug,
                'name' => $this->name,
                'user_id' => $this->user_id,
                'description' => $this->description,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            $request
        );
    }
}
