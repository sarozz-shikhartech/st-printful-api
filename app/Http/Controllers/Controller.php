<?php

namespace App\Http\Controllers;

use App\ResponseTrait;
use App\Services\PrintfulService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;

abstract class Controller
{
    use ResponseTrait;

    protected PrintfulService $pfService;

    /**
     * @param PrintfulService $pfService
     */
    public function __construct(PrintfulService $pfService)
    {
        $this->pfService = $pfService;
    }

    /**
     * @throws GuzzleException
     */
    public function clientRequest($method, $uri, $options = []): \Exception|string
    {
        try {
            $client = new Client();
            $req = $client->request($method, $uri, $options);

            return $req->getBody()->getContents();
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function config($key): mixed
    {
        $config = Config::get('app');
        return $config[$key] ?? NULL;
    }
}
