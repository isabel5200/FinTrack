<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditBudgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category_name,
            'max_amount' => '$' . number_format($this->max_amount, 2),
            'frequency' => ucfirst($this->frequency),
        ];
    }
}
