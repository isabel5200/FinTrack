<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => '$' . number_format($this->amount, 2),
            'type' => ucfirst($this->type),
            'category' => $this->category_name,
            'description' => $this->description,
            'attachment' => $this->attachment
                ? route('transactions.viewFile', ['transaction' => $this->id])
                : null,
            'date' => $this->date,
        ];
    }
}
