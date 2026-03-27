<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_category_id' => ['required', 'integer', 'exists:ticket_categories,id'],
            'quantity'           => ['required', 'integer', 'min:1', 'max:4'],
            'idempotency_key'    => ['required', 'string', 'unique:reservations,idempotency_key'],
        ];
    }
}
