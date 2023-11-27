<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
{
    private array $types = ['C' => 'Concurso','V' => 'Vestibular'];
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email
            ],
            'type' => $this->types[$this->type],
            'test_date' => $this->test_date,
            'content_to_study' => $this->content_to_study,
            'studyBlocksCount' => $this->studyBlocks->count(),
            'studyBlocksCountCompleted' => $this->studyBlocks->where('completed','=', '1')->count()
        ];
    }
}
