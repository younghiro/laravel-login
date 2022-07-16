<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginFormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /*
    @return view
    */
    public function showLogin(){
        return view('login.login_form');
    }

    /*
    @param App\Http\Requests\LoginFormRequest
    */
    public function login( LoginFormRequest $request){
        $credentials = $request->only('email','password');

        //1.アカウントがロックされてたら弾く
        $user = User::where('email', '=' , $credentials['email'])->first();

        if($user->locked_flg ===1){
            return back()->withErrors(['danger' =>'アカウントがロックされています。']);
        }

        if(!is_null($user)){
            if(Auth::attempt($credentials)){
                $request->session()->regenerate();
                //2. 成功したらエラーカウントを０にする
                if($user->error_count >0){
                    $user->error_count=0;
                    $user->save();
                }
                return redirect()->route('home')->with('success','ログイン成功しました');
            }

            //3.ログイン失敗したらエラーカウントを１増やす
            $user->error_count = $user->error_count + 1;
            //4.エラーカウントが６以上の場合はアカウントをロックする
            if( $user->error_count >5){
                $user->locked_flg = 1;
                $user->save();
                return back()->withErrors([ 'danger' => 'アカウントがロックされました。解除したい場合は運営にご連絡ください。']);
            }
        }
        
        return back()->withErrors([ 'danger' => 'メールアドレスかパスワードが間違っています。']);
    }
}
