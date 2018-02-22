<?php

namespace App\Http\Controllers\Sentinel;

use Reminder;
use Activation;
use Sentinel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SentinelController extends Controller
{
    /**
     * resend activation
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function resendActivationCode(Request $request) {
        // 古いアクティベーションコードを削除
        Activation::removeExpired();

        // ユーザーを確認
        $user = Sentinel::findByCredentials(['email' => base64_decode($request->email)]);
        if (is_null($user)) {
            return redirect('login')->with(['myerror' => trans('sentinel.invalid_activation_params')]);
        }
        // すでにアクティベート済みの時は、何もせずにログインへ
        if (Activation::completed($user)) {
            return redirect('login')->with(['info' => trans('sentinel.activation_done')]);
        }
        // アクティベーションの状況を確認
        $exists = Activation::exists($user);
        if (!$exists) {
            // 存在しない場合は、再生成して、そのコードを送信する
            $activation = Activation::create($user);
        }else {
            // 現在のコードを
            $activation = $exists;
        }
        // メールで送信する
        $this->sendActivationCode($user, $activation->code);

        // メールを確認して、承認してからログインすることを表示するページへ
        return redirect('login')->with('info', trans('sentinel.after_register'));
    }

    /**
     * send reset password mail
     * ユーザーが有効で、パスワードが条件に合致していたら、SentinelのReminderを使って処理する
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse 
     */
    protected function sendResetPassword(Request $request) {
        // 古いリマインダーコードを削除
        Reminder::removeExpired();

        // チェック
        $this->validate($request, [
           'email' => 'required|email|max:255',
           'password' => 'required|between:6,255|confirmed',
       ]);
       // ユーザーを検索
       $user = Sentinel::findByCredentials(['email'=>$request->email]);
       if (is_null($user)) {
           // ユーザーがいなければ成功したような感じにしてしれっとログイン画面へ
           return redirect('login')->with(['info'=>trans('sentinel.password_reset_sent')]);
       }
       // リマインダーが作成済みなら、それを再送信する
       $code = "";
       $exists = Reminder::exists($user);
       if ($exists) {
           // すでに設定されているので、リマインダーコードを設定
           $code = $exists->code;
       }
       else {
           // 新規にリマインダーを作成して、コードを返す
           $reminder = Reminder::create($user);
           $code = $reminder->code;
       }
       // メールを送信
       Mail::send('sentinel.emails.reminder', [
           'user' => $user,
           'code' => $code,
           'password' => $request->password,
       ], function($m) use ($user) {
           $m->from(config('app.activation_from'), config('app.appname'));
           $m->to($user->email, $user->name)->subject(trans('sentinel.reminder_title'));
       });
       // 成功したら、login画面へ移動
       return redirect('login')->with(['info'=>trans('sentinel.password_reset_sent')]);
    }

    /**
     * password reset
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse 
     */
    protected function resetPassword(Request $request) {
        // データ長を調整しておく
        $email = substr(base64_decode($request->email), 0, 255);
        $code = substr($request->code, 0, 64);
        $passwd = substr(base64_decode($request->password), 0,255);

        $user = Sentinel::findByCredentials(['email' => $email]);
        if (is_null($user)) {
            // 不正なアクセスだが、正常に終わったようなメッセージを返す
            return redirect('login')->with('info', trans('sentinel.password_reset_done'));
        }

        // リマインダーを完了させる
        if (Reminder::complete($user, $code, $passwd)) {
            // 成功
            return redirect('login')->with('info', trans('sentinel.password_reset_done'));
        }
        // 失敗
        return redirect('login')->with('info', trans('sentinel.password_reset_failed'));
    }
}
