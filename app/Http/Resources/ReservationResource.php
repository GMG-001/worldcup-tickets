<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->getId(),
            'ticket_category_id' => $this->getTicketCategoryId(),
            'quantity'           => $this->getQuantity(),
            'status'             => $this->getStatus()->value,
            'expires_at'         => $this->getExpiresAt(),
            'ticket_category'    => new TicketCategoryResource($this->whenLoaded('ticketCategory')),
        ];
    }
}
