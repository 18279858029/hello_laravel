<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//引用Auth
use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest',[
            'only' => ['create']
            ]);
    }
    //登录页
    public function create()
    {
    	return view('sessions.create');
    }
    //
    public function store(Request $request)
    {
    	$credentials = $this->validate($request, [
           'email' => 'required|email|max:255',
           'password' => 'required'
       ]);
        
        if (Auth::attempt($credentials,$request->has('remember'))){
        	//登录操作
        	session()->flash('success','欢迎回来');
        	return redirect()->intended(route('users.show',[Auth::user()]));
        } else {
        	//登录返回
        	session()->flash('success','邮箱或密码错误,请重试！');
        	return redirect()->back();
        }
    }

    //退出
    public function destory()
    {
        Auth::logout();
        session()->flash('success','您已成功退出！');
        return redirect('login');
    }
}
