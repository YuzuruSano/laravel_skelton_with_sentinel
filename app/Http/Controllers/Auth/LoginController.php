<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Sentinel;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Where to redirect users after logout.
     *
     * @var string
     */
    protected $logoutTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * login
     * @param  Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {
        // バリデーション
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required|between:6,255',
            'remember' => 'boolean',
        ]);

        // 認証処理
        try {
            $this->userInterface = Sentinel::authenticate([
                'email' => $request['email'],
                'password' => $request['password']
            ], $request['remember']);
        } catch (NotActivatedException $notactivated) {
            return view('auth.login', [
                'resend_code' => $request['email']
            ])->withErrors([trans('sentinel.not_activation')]);
        } catch (ThrottlingException $throttling) {
            return view('auth.login')->withErrors([trans('sentinel.login_throttling')."[あと".$throttling->getDelay()."秒]"]);
        }

        if (!$this->userInterface) {
            // エラー
            return view('auth.login')->withErrors([trans('sentinel.login_failed')]);
        }

        return redirect($this->redirectTo);
    }

    /**
     * logout
     * @param  Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    protected function logout(Request $request) {
        Sentinel::logout();
        return redirect($this->logoutTo);
    }

}
