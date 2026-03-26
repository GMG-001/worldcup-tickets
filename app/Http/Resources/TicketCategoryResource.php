<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->getId(),
            'match_id'   => $this->getMatchId(),
            'name'       => $this->getName(),
            'price'      => $this->getPrice(),
            'seat_count' => $this->getSeatCount(),
            'match'      => new FootballMatchResource($this->whenLoaded('match')),
        ];
    }
}
