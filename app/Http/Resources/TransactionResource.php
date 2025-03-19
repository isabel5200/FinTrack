<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->amount,
            'type' => $this->type,
            'category' => $this->category_name,
            'description' => $this->description,
            'date' => $this->date,
        ];
    }
}
