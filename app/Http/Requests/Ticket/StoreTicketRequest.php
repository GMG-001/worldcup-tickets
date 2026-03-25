<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reservation_id' => ['required', 'integer', 'exists:reservations,id'],
            'match_id'       => ['required', 'integer', 'exists:matches,id'],
            'category_id'    => ['required', 'integer', 'exists:ticket_categories,id'],
            'seat_number'    => ['required', 'string', 'max:255'],
            'qr_code'        => ['required', 'string', 'unique:tickets,qr_code'],
        ];
    }
}
