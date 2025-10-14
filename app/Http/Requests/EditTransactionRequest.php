<?php

// required|integer|exists:categories,id

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EditTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $transactionId = $this->route('transaction')->id ?? $this->route('transaction');

        return [
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|in:expense,income',
            'category' => [
                'required',
                'integer',
                Rule::unique('transactions', 'category_id')
                    ->ignore($transactionId),
            ],
            'description' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
            'date' => 'required|date',
        ];
    }
}
