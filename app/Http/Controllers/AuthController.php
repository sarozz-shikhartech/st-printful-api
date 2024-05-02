<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AuthController extends Controller
{

    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());

        // validate the request
        $validator = Validator::make($request->all(), [
            'storeId' => 'required',
            'storeUrl' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) return $this->output($validator->errors(), [], 422);

        $storeName = $data->name;
        $email = $data->email;
        $password = $data->password;
        $storeId = $data->storeId;
        $storeUrl = $data->storeUrl;

        try {
            DB::beginTransaction();
            $newUser = User::create([
                'name'       => $storeName,
                'email'      => $email,
                'password'   => Hash::make($password),
                'status'     => '1',
                'store_id'   => $storeId,
                'store_url'  => $storeUrl
            ]);

            $token = Auth::claims(['user_id' => $newUser->id, 'email' => $newUser->email, 'store_id' => $newUser->store_id, 'store_url' => $newUser->store_url])
                ->login($newUser);
            if (!$token) return $this->output('Something went wrong', [], 500);


            try {
                //setting up webhook for the user
                $setupWebhook = $this->clientRequest('POST','https://api.printful.com/v2/webhooks',
                    [
                        'headers' => [
                            'X-PF-Store-Id' => $storeId,
                            'Authorization' => 'Bearer '. $this->config('printful.access_token')
                        ],
                        'json' => [
                            'default_url' => $this->config('app_url') . '/api/pf/webhook',
                            'events' => [
                                [
                                    'type' => 'shipment_sent',
                                    'url' => $this->config('app_url') . '/api/pf/webhook'
                                ],
                                [
                                    'type' => 'order_created',
                                    'url' => $this->config('app_url') . '/api/pf/webhook'
                                ],
                                [
                                    'type' => 'order_canceled',
                                    'url' => $this->config('app_url') . '/api/pf/webhook'
                                ]
                            ]
                        ]
                    ]);


            } catch (GuzzleException $exception) {
                DB::rollBack();
                return $this->output('Registration failed.', $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $data = [
                "store_name" => $newUser->name,
                "token" => $token,
            ];

            DB::commit();
            return $this->output('User created successfully.', $data);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->output('Registration failed.', $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Expects email and password and attempts to log in the active users
     */
    public function login(Request $request): JsonResponse
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) return $this->output($validator->errors(), [], 422);

        // fetch a user with requested email and active status
        $user = User::where([['email', $request->email], ['status', '1']])->first();
        if (!$user) return $this->output('User not found', [], 422);

        $credentials = $request->only('email', 'password');

        /*
         * Creating JWT token with additional data of user
         * i.e : user's id and email
         */
        $token = Auth::claims(['user_id' => $user->id, 'email' => $user->email])->attempt($credentials);
        if (!$token) return $this->output('Invalid credentials.',[], 401);

        $data['email'] = $user->email;
        $data['name'] = $user->name;
        $data['token'] = $token;

        return $this->output('Login successfully.', $data);
    }
}
