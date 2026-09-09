<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scrape extends Model
{
    use HasFactory;

    // Explicitly target your custom table name
    protected $table = 'n8n_ai_competitor_monitoring';

    // Primary key configuration (UUID char(36))
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Disable default Laravel created_at/updated_at handling
    public $timestamps = false;

    // Allow mass assignment for all columns
    protected $fillable = [
        'id',
        'timestamp',
        'competitor_name',
        'post_type',
        'threat_level',
        'original_khmer_text',
        'english_summary',
        'ai_counter_strategy_draft',
        'source_url',
        'image_url',
        'created_at',
    ];

    // Cast datetime strings to Carbon instances
    protected $casts = [
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
    ];
}
