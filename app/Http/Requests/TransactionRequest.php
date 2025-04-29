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
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|in:expense,income',
            'category' => 'required|integer|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
            'date' => 'required|date',
        ];
    }
}
