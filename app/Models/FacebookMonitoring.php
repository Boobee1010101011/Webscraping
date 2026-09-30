<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookMonitoring extends Model
{
    use HasFactory;

    protected $table = 'n8n_facebook_monitoring';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id', 'timestamp', 'competitor_name', 'country', 'post_type',
        'threat_level', 'original_text', 'english_summary',
        'ai_counter_strategy_draft', 'source_url', 'image_url',
        'content_hash', 'created_at',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
    ];
}
