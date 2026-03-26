<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->getId(),
            'reservation_id' => $this->getReservationId(),
            'match_id'       => $this->getMatchId(),
            'category_id'    => $this->getCategoryId(),
            'seat_number'    => $this->getSeatNumber(),
            'status'         => $this->getStatus()->value,
            'qr_code'        => $this->getQrCode(),
            'match'          => new FootballMatchResource($this->whenLoaded('match')),
            'category'       => new TicketCategoryResource($this->whenLoaded('category')),
        ];
    }
}
