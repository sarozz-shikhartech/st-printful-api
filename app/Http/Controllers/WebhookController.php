<?php

namespace App\Http\Controllers;

use App\Models\OrderLog;
use App\Models\OrderShipmentLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    public function webhookHandler(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent());
        $data = $content["data"];

        if (!empty($result["type"])) {

            /*
             * https://developers.printful.com/docs/v2-beta/#tag/Webhook-v2/operation/shipmentSent
             */
            if ($data["type"] === "shipment_sent") {
                $orderLog = $this->pfService->serviceOrderCreate($data);

                $shipment = $data["shipment"];

                OrderShipmentLog::create([
                    'order_log_id' => $orderLog->id,
                    'shipment_id' => $shipment["id"],
                    'status' => $shipment["status"],
                    'tracking_number' => $shipment["tracking_number"],
                    'ship_date' => $shipment["ship_date"],
                    'ship_at' => $shipment["shipped_at"],
                    'reshipment' => $shipment["reshipment"],
                ]);
            }

            /*
             * https://developers.printful.com/docs/v2-beta/#tag/Webhook-v2/operation/orderCreated
             */
            if ($data["type"] === "order_created") {
                $orderLog = $this->pfService->serviceOrderCreate($data);
            }

            /*
             * https://developers.printful.com/docs/v2-beta/#tag/Webhook-v2/operation/orderCanceled
             */
            if ($data["type"] === "order_canceled") {
                $orderLog = $this->pfService->serviceOrderCreate($data);
            }

            return $this->output('Log Created.');
        }

        return $this->output('Fail to retrieve webhook type.', [], 500);
    }
}
