<?php

namespace App\Http\Requests\TicketCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => ['sometimes', 'string', 'max:255'],
            'price'           => ['sometimes', 'numeric', 'min:0'],
            'seat_count'      => ['sometimes', 'integer', 'min:1'],
            'available_count' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
