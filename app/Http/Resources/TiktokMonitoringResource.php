<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TiktokMonitoringResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'timestamp' => $this->timestamp?->toISOString(),
            'competitor_name' => $this->competitor_name, 'country' => $this->country,
            'post_type' => $this->post_type, 'threat_level' => $this->threat_level,
            'caption' => $this->caption, 'transcript' => $this->transcript,
            'english_summary' => $this->english_summary,
            'ai_counter_strategy_draft' => $this->ai_counter_strategy_draft,
            'source_url' => $this->source_url, 'image_url' => $this->image_url,
            'content_hash' => $this->content_hash, 'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
