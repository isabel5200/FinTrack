<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => 'required|integer|exists:categories,id',
            'max_amount' => 'required|numeric|min:0',
            'frequency' => 'required|string|in:daily,weekly,monthly',
        ];
    }
}
