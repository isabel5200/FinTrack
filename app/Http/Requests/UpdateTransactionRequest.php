<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
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
            'category' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('user_id', $this->user()->id)
                        ->where('type', $this->input('type'));
                }),
            ],
            'description' => 'nullable|string|max:255',
            'attachment' => 'sometimes|nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
            'remove_attachment' => 'sometimes|boolean',
            'date' => 'required|date',
        ];
    }
}
