<?php

namespace App\Services;

use App\Models\OrderLog;
use App\ResponseTrait;

class PrintfulService
{
    use ResponseTrait;

    public function serviceOrderCreate($data)
    {
        $order = $data["order"];

        return OrderLog::create([
            'order_no' => $order['order_no'],
            'external_id' => $order['external_id'],
            'status' => $order['status'],
            'type' => $data['type'],
            'store_id' => $order['store_id'],
            'order_date' => $order['order_date'],
            'remarks' => $data['reason'] ?? null,
        ]);
    }
}
