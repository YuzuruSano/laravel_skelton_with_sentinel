<?php

namespace App\Http\Controllers\Auth;

use Activation;
use Mail;
use Sentinel;
use App\User;
use App\Http\Controllers\Controller;
use App\Notifications\RegisterNotify;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|between:6,255|confirmed',
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $credentials = [
            'email' => $request['email'],
            'password' => $request['password'],
        ];

        $user = Sentinel::register($credentials);

        //create activation
        $activation = Activation::create($user);

        // send notify mail
        $usermodel = User::where('email', $user->email)->get()[0];
        $usermodel->notify(new RegisterNotify($activation->code));

        // メールを確認して、承認してからログインすることを表示するページへ
        return redirect($this->redirectTo)->with('info', trans('sentinel.after_register'));
    }
}
