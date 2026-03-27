<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FootballMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->getId(),
            'home_team_en' => $this->getHomeTeamEn(),
            'home_team_ka' => $this->getHomeTeamKa(),
            'away_team_en' => $this->getAwayTeamEn(),
            'away_team_ka' => $this->getAwayTeamKa(),
            'stadium_en'   => $this->getStadiumEn(),
            'stadium_ka'   => $this->getStadiumKa(),
            'match_date'   => $this->getMatchDate(),
            'categories'   => TicketCategoryResource::collection($this->whenLoaded('ticketCategories')),
        ];
    }
}
