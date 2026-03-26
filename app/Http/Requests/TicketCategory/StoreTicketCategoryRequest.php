<?php

namespace App\Http\Requests\TicketCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'match_id'        => ['required', 'integer', 'exists:matches,id'],
            'name'            => ['required', 'string', 'max:255'],
            'price'           => ['required', 'numeric', 'min:0'],
            'seat_count'      => ['required', 'integer', 'min:1'],
            'available_count' => ['required', 'integer', 'min:0'],
        ];
    }
}
