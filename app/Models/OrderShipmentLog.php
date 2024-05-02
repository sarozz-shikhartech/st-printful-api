<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShipmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_log_id',
        'shipment_id',
        'status',
        'tracking_number',
        'ship_date',
        'ship_at',
        'reshipment'
    ];

    public function orderLog(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrderLog::class);
    }
}
