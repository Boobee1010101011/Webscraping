<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankMonitoring extends Model
{
    use HasFactory;

    protected $table = 'n8n_bank_monitoring';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'timestamp',
        'bank_name',
        'bank_type',
        'original_text',
        'english_summary',
        'source_url',
        'image_url',
        'content_hash',
        'created_at',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
    ];
}
