<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MainController extends Controller
{

    /**
     * @throws GuzzleException
     */
    public function getPrintFulProductById(Request $request, int $productId): JsonResponse
    {
        $res = $this->clientRequest('get','https://api.printful.com/products/' . $productId);

        if ($res instanceof \Exception) {
            return $this->output('Product not found.', $res->getMessage(), 500);
        }

        $data = json_decode($res, true);

        return $this->output('Product data fetched.', $data['result']);
    }

    /**
     * @throws GuzzleException
     */
    public function printFulCreateOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $res = $this->clientRequest('post','https://api.printful.com/orders', [
            'headers' => [
                'Authorization' => 'Bearer '. $this->config('printful.access_token'),
                'X-PF-Store-Id' => $data['printful_store_id']
            ],
            'json' => $data
        ]);

        if ($res instanceof \Exception) {
            return $this->output('Product not found.', $res->getMessage(), 500);
        }

        $data = json_decode($res, true);
        if ($data['code'] !== 200) {
            return $this->output($data['result'], [], $data['code']);
        }

        return $this->output('Order created.', $data['result']);
    }
}
