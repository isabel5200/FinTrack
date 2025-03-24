<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'type' => 'required|string|in:expense,income',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'attachment' => 'nullable|file',
            'date' => 'required|date',
        ];
    }
}
