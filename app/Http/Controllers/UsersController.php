<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\User;

use Auth;
class UsersController extends Controller
{
	//过滤
	public function __construct()
	{
		$this->middleware('auth',[
           'except' => ['show','create','store','index']
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
    	return view('users.show',compact('user'));
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
    	//让认证通过的用户直接通过
    	Auth::login($user);
    	session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
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
        $user -> delete();
        session()->flash('success','删除成功');
        return back();
    }
}
