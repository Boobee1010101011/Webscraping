<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScrapeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'competitor_name' => $this->competitor_name,
            'post_type' => $this->post_type,
            'threat_level' => $this->threat_level,
            'original_khmer_text' => $this->original_khmer_text,
            'english_summary' => $this->english_summary,
            'ai_counter_strategy_draft' => $this->ai_counter_strategy_draft,
            'source_url' => $this->source_url,
            'image_url' => $this->image_url,
            'posted_at' => [
                'raw' => $this->timestamp?->toISOString(),
                'formatted' => $this->timestamp
                    ? $this->timestamp->timezone('Asia/Phnom_Penh')->format('M d, Y - h:i A')
                    : null,
            ],
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
