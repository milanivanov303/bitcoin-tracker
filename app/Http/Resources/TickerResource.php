<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TickerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'lastPrice'    => $this[6] ?? null,
            'dailyHighest' => $this[8] ?? null,
            'dailyLowest'  => $this[9] ?? null,
        ];
    }
}
