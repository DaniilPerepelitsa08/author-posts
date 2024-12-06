<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetAuthorsPostsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'columns' => 'nullable|array',
            'columns.*' => 'required|string',
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1|max:50',
            'sort_column' => 'nullable|string',
            'sort_direction' => 'in:asc,desc',
            'filter_column' => 'nullable|string',
            'filter_value' => 'nullable|string',
            'include_totals' => 'boolean',
            'name' => 'nullable|string',
        ];
    }
}
