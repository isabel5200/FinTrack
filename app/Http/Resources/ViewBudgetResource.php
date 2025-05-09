<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewBudgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'category' => $this->category->name,
            'max_amount' => '$' . number_format($this->max_amount, 2),
            'frequency' => ucfirst($this->frequency),
            'created' => $this->created_at->format('m-d-Y H:i'),
            'updated' => $this->updated_at->format('m-d-Y H:i'),
        ];
    }
}
