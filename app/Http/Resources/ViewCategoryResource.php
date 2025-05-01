<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'type' => ucfirst($this->type),
            'created' => $this->created_at->format('m-d-Y H:i'),
            'updated' => $this->updated_at->format('m-d-Y H:i'),
        ];
    }
}
