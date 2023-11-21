<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyBlockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'goal' => new GoalResource($this->goal),
            'schedule' => new ScheduleResource($this->schedule),
            'date' => $this->date,
            'content' => $this->content,
            'completed' => $this->completed
        ];
    }
}
