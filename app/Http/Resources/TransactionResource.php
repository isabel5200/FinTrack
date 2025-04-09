<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => '$' . number_format($this->amount, 2),
            'type' => ucfirst($this->type),
            'category' => $this->category_name,
            'description' => $this->description,
            'date' => Carbon::parse($this->date)->format('m-d-Y'),
        ];
    }
}
