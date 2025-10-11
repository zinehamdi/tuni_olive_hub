<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'carrier_id','load_ids','route_polyline','start_at','delivered_at','distance_km','earning','sr_code','pin_token','pin_hash','pin_qr'
    ];

    protected $casts = [
        'load_ids' => 'array',
        'start_at' => 'datetime',
        'delivered_at' => 'datetime',
        'distance_km' => 'decimal:2',
        'earning' => 'decimal:3',
    ];

    public function carrier(){ return $this->belongsTo(User::class, 'carrier_id'); }
    public function pods(){ return $this->hasMany(Pod::class); }

    public const ST_DRAFT = 'draft';
    public const ST_STARTED = 'started';
    public const ST_POD_SUBMITTED = 'pod_submitted';
    public const ST_COMPLETED = 'completed';

    // Trips table does not currently have status column; we infer from timestamps.
    public function inferredStatus(): string
    {
        if ($this->delivered_at) return self::ST_COMPLETED;
        if ($this->pods()->exists()) return self::ST_POD_SUBMITTED;
        if ($this->start_at) return self::ST_STARTED;
        return self::ST_DRAFT;
    }

    public static function generatePin(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function hashPin(string $pin): string
    {
        return hash('sha256', 'trip-pin:'.$pin);
    }
}
