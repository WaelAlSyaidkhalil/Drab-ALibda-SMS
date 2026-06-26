<?php

namespace App\Http\Resources\Parent;

use Illuminate\Http\Resources\Json\JsonResource;

class ChildResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'classroom' => $this->getCurrentClass()?->name,
            'section' => $this->getCurrentSection()?->name,
            'image' => $this->user?->avatar,
        ];
    }
}
