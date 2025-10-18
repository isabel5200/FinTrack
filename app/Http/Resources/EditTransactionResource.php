<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'type' => $this->type,
            'category' => $this->category->id,
            'description' => $this->description,
            'attachment' => $this->attachment
                ? route('transactions.viewFile', ['transaction' => $this->id])
                : null,
            'date' => Carbon::parse($this->date)->format('m/d/Y'),
        ];
    }
}
