<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginFormRequest;
use Illuminate\Support\Facades\Auth;

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

        $credentials = $request->only( 'email', 'password'); //onlyは指定したキー名のキー名と値をセットで取得する。

        if(Auth::attempt($credentials)){  //attempは認証を行っている
            $request->session()->regenerate();

            return redirect()->route('home')->with('success','ログイン成功しました。');
        }
        //withもwithErrorsもsessionで返すことができる
        return back()->withErrors([
            'danger'=>'メールアドレスかパスワードが間違えてます。',
        ]);
    }

    /*
    return view
    */
    public function showHome(){
        return view('login.home');
    }

    /**
     * ユーザーをアプリケーションからログアウトさせる
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('showLogin')->with('danger','ログアウトしました。');
    }
}
