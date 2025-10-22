<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $budgetId = $this->route('budget')->id ?? $this->route('budget');

        return [
            'category' => [
                'required',
                'integer',
                Rule::unique('budgets', 'category_id')
                    ->where(function ($query) {
                        return $query
                            ->where('user_id', Auth::user()->id)
                            ->where('frequency', $this->input('frequency'));
                    })
                    ->ignore($budgetId),
            ],
            'max_amount' => 'required|numeric|min:0',
            'frequency' => 'required|string|in:daily,weekly,monthly',
        ];
    }
}
