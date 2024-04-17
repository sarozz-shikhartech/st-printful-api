<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            'name'     => 'required|string',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) return $this->output($validator->errors(), [], 422);

        $name = $data->name;
        $email = $data->email;
        $password = $data->password;

        try {
            $newUser = User::create([
                'name'       => $name,
                'email'      => $email,
                'password'   => Hash::make($password),
                'status'     => '1'
            ]);

            return $this->output('User created successfully.', $newUser);
        } catch (\Exception $exception) {
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
