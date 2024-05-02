<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'external_id',
        'status',
        'type',
        'store_id',
        'remarks',
        'order_date'
    ];

    public function orderShipmentLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderShipmentLog::class, 'order_log_id', 'id');
    }
}
