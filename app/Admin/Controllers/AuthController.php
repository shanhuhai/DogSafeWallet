<?php

namespace App\Admin\Controllers;

use App\Group;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseAuthController
{
    protected $redirectTo = '/admin/wallets';

    public function getRegister()
    {
        return view('admin.register');
    }

    public function postRegister(Request $request)
    {
        $this->validator($request->all())->validate();

        DB::transaction(function() use($request){
            $user = $this->create($request->all());

            //默认设置为会员
            $user->roles()->sync([3]);
            //添加默认分组
            Group::query()->create([
                'user_id'=>$user->id ,
                'name'=>'默认分组'

            ]);

            $this->guard()->login($user);
        });



        return view('admin.register_success');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => ['required', 'string', 'min:2','max:50','unique:admin_users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admin_users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return Administrator::create([
            'username' => $data['username'],
            'name' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
