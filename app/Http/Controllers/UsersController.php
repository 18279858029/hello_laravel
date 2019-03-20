<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\User;

use Auth;

use Mail;

class UsersController extends Controller
{
	//过滤
	public function __construct()
	{
		$this->middleware('auth',[
           'except' => ['show','create','store','index','confirmEmail']
			]);
        $this->middleware('guest',[
           'only' => ['create']
            ]);
	}

    public function index()
    {
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

	//注册页
    public function create()
    {

    	return view('users.create');
    }
    //
    public function show(User $user)
    {
        $statuses = $user->statuses()->orderBy('created_at','desc')->paginate(30);
    	return view('users.show',compact('user','statuses'));
    }

    public function store(Request $request)
    {
    	$this->validate($request,[
    		'name' => 'required|max:50',
    		'email' => 'required|email|unique:users|max:255',
    		'password' => 'required|confirmed|min:6']);
    	$user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => bcrypt($request->password)
    		]);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success','验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    	//让认证通过的用户直接通过
    	Auth::login($user);
    	session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
    	return redirect()->route('users.show',[$user]);
    }

    //发送验证邮箱
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = '感谢注册 Sample 应用！请确认你的邮箱。';

     Mail::send($view,$data,function ($message) use ($to,$subject){
      $message->from($from,$name)->to($to)->subject($subject);
        });

    }
    
    //邮箱激活成功
    public function confirmEmail($token)
    {
        $user = User::where('activation_token',$token)->firstOrFail();
        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success','恭喜您注册成功');
        return redirect()->route('users.show',[$user]);
    }

    //修改页
    public function edit(User $user)
    {
    	$this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    //更新用户信息
    public function update(User $user,Request $request)
    {
        $this->validate($request,[
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'  
        	]);
 		$data = [];
 		$data['name'] = $request->name;
 		if ($request->password){
 			$data['password'] = bcrypt($request->password);
 		}
        $user->update($data);
        session()->flash('success','个人资料更新成功');
        return redirect()->route('users.show',$user->id);
    }

   //删除用户
    public function destory(User $user)
    {
        $this -> authorize('destory',$user);        
        $user -> delete();
        session()->flash('success','删除成功');
        return back();
    }
}
