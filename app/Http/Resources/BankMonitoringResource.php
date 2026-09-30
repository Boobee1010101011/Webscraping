<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankMonitoringResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'timestamp' => $this->timestamp?->toISOString(),
            'bank_name' => $this->bank_name, 'bank_type' => $this->bank_type,
            'original_text' => $this->original_text, 'english_summary' => $this->english_summary,
            'source_url' => $this->source_url, 'image_url' => $this->image_url,
            'content_hash' => $this->content_hash, 'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
