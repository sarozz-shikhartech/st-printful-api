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
    public function getPrintFulStoreSyncProducts(Request $request, int $storeId): JsonResponse
    {
        /*
         * https://developers.printful.com/docs/v2-beta/#tag/Sync-v2/operation/getSyncProducts
         */
        $res = $this->clientRequest('get','https://api.printful.com/v2/sync-products', [
            'headers' => [
                'X-PF-Store-Id' => $storeId,
                'Authorization' => 'Bearer '. $this->config('printful.access_token')
            ]
        ]);

        if ($res instanceof \Exception) {
            return $this->output('Error when fechting products', $res->getMessage(), 500);
        }

        $data = json_decode($res, true);

        return $this->output('Product data fetched.', $data['result']);
    }

    /**
     * @throws GuzzleException
     */
    public function getPrintFulStoreSyncProductById(Request $request, int $storeId, int $productId): JsonResponse
    {
        if (!$storeId || !$productId) {
            return $this->output('Invalid request data. Make sure to add store Id and product Id in url.');
        }

        /*
         * https://developers.printful.com/docs/v2-beta/#tag/Sync-v2/operation/getSyncProductById
         */
        $res = $this->clientRequest('GET','https://api.printful.com/v2/sync-products/' . $productId,
        [
            'headers' => [
                'X-PF-Store-Id' => $storeId,
                'Authorization' => 'Bearer '. $this->config('printful.access_token')
            ]
        ]);

        if ($res instanceof \Exception) {
            return $this->output('Product not found.', $res->getMessage(), 500);
        }

        $data = json_decode($res, true);
        return $this->output('Product data fetched.', $data['data']);
    }

    /**
     * @throws GuzzleException
     */
    public function getSyncProductVariantsById(Request $request, int $storeId, int $productId): JsonResponse
    {
        if (!$storeId || !$productId) {
            return $this->output('Invalid request data. Make sure to add store Id and product Id in url.');
        }

        /*
         * https://developers.printful.com/docs/v2-beta/#tag/Sync-v2/operation/getSyncProductVariants
         */
        $res = $this->clientRequest('GET','https://api.printful.com/v2/sync-products/' . $productId .'/sync-variants',
            [
                'headers' => [
                    'X-PF-Store-Id' => $storeId,
                    'Authorization' => 'Bearer '. $this->config('printful.access_token')
                ]
            ]);

        if ($res instanceof \Exception) {
            return $this->output('Product not found.', $res->getMessage(), 500);
        }

        $data = json_decode($res, true);
        return $this->output('Product data fetched.', $data['data']);
    }

    /**
     * @throws GuzzleException
     */
    public function printFulCreateOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /*
         * https://developers.printful.com/docs/v2-beta/#tag/Orders-v2/operation/createOrder
         */
        $res = $this->clientRequest('post','https://api.printful.com/v2/orders', [
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
        return $this->output('Order created.', $data['data']);
    }

    public function printFulConfirmOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /*
         * https://developers.printful.com/docs/v2-beta/#tag/Orders-v2/operation/confirmOrder
         */
        $res = $this->clientRequest('post','https://api.printful.com/v2/orders/'. $data["order_id"] .'/confirmation', [
            'headers' => [
                'Authorization' => 'Bearer '. $this->config('printful.access_token'),
                'X-PF-Store-Id' => $data['printful_store_id']
            ],
            'json' => $data
        ]);

        if ($res instanceof \Exception) {
            return $this->output('Product not found.', $res->getMessage(), 500);
        }

        return $this->output('Order Updated.', $data['data']);
    }
}
