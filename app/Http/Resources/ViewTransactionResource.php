<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'amount' => '$' . number_format($this->amount, 2),
            'type' => ucfirst($this->type),
            'category' => $this->category->name,
            'description' => $this->description,
            'attachment' => $this->attachment
                ? route('transactions.viewFile', ['transaction' => $this->id])
                : null,
            'date' => $this->date,
            'created' => $this->created_at->format('m-d-Y H:i'),
            'updated' => $this->updated_at->format('m-d-Y H:i'),
        ];
    }
}
