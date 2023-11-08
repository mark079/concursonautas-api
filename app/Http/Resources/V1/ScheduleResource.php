<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    private array $weekdays = [0 => 'Segunda', 1 => 'Terça', 2 => 'Quarta', 3 => 'Quinta', 4 => 'Sexta', 5 => 'Sábado', 6 => 'Domingo'];
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'goal' => new GoalResource($this->goal),
            'weekday' => $this->weekdays[$this->weekday],
            'start_time' => $this->start_time,
            'end_time' => $this->end_time
        ];
    }
}
