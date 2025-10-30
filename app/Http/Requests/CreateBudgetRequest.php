<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CreateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => [
                'required',
                'integer',
                'exists:categories,id',
                Rule::unique('budgets', 'category_id')
                    ->where(function ($query) {
                        return $query
                            ->where('user_id', Auth::user()->id)
                            // ->where('category_id', $this->input('category'))
                            ->where('frequency', $this->input('frequency'));
                    }),
            ],
            'max_amount' => 'required|numeric|min:0',
            'frequency' => 'required|string|in:daily,weekly,monthly',
        ];
    }
}
