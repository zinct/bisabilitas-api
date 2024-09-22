<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Models\User;
use App\Traits\ApiTrait;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ApiTrait;

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->sendResponse('Invalid credentials!', code: 401);
        }

        $user = Auth::user();
        $data['token'] = $user->createToken(env("APP_KEY"))->plainTextToken;
        $data['user'] = $user;

        return $this->sendResponse('Login successfully', data: $data);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);
        $user = Auth::user();
        $data['token'] = $user->createToken(env("APP_KEY"))->plainTextToken;
        $data['user'] = $user;

        return $this->sendResponse('Register successfully', data: $data);
    }

    public function oauth(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:google',
            'accessToken' => 'required',
        ]);


        try {
            $providerUser = Socialite::driver($request->provider)->stateless()->userFromToken($request->accessToken);
        } catch (RequestException $exception) {
            return $this->sendResponse('Invalid credentials!', code: 401);
        } catch (Exception $exception) {
            return $this->sendResponse($exception->getMessage(), code: 401);
        }

        $linkedSocialAccount =  SocialAccount::where('provider_name', $request->provider)
            ->where('provider_id', $providerUser->getId())
            ->first();
        if ($linkedSocialAccount) {
            Auth::login($linkedSocialAccount->user);
            $user = Auth::user();
            $data['token'] = $user->createToken(env("APP_KEY"))->plainTextToken;
            $data['user'] = $user;

            return $this->sendResponse('Login successfully', data: $data);
        } else {
            $user = User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
            ]);
            $user->markEmailAsVerified();

            $user->socialAccount()->create([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $request->provider,
            ]);

            Auth::login($user);

            if (Auth::check()) {
                $user = Auth::user();
                $data['token'] = $user->createToken(env("APP_KEY"))->plainTextToken;
                $data['user'] = $user;

                return $this->sendResponse('Login successfully', data: $data);
            }
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->user()->tokens()->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->sendResponse('Logout successfully');
    }
}
