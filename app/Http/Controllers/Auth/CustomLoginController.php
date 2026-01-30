<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class CustomLoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect('admin/dashboard');
        }
        $data['title'] = 'Login';
        return view('auth.login', $data);
    }

    public function post_login(Request $request)
    {
        Validator::make($request->all(), [
            'username'     => 'required',
            'password'      => 'required',
        ])->validate();

        $credentials  = array('username' => $request->username, 'password' => $request->password);

        $user = User::where('username', $credentials['username'])->first();

        if (@$user) {
            if ($user && Hash::check($credentials['password'], $user->password)) {
                Auth::attempt(['username' => $request->username, 'password' => $request->password]);

                $session = [
                    'username'                  => $user->username,
                    'name'                      => $user->name,
                    'email'                     => $user->email,
                    'currYear'                  => date('Y'),
                    'group_id'                  => @$user->ms_group_id,
                    'group_alias'               => @$user->group->alias,
                    'group_name'                => @$user->group->name,
                ];
                session($session);

                return redirect('admin/dashboard')->with([
                    'message' => trans('Selamat datang kembali'),
                    'type'    => "success"
                ]);
            } else {
                return redirect('/login')
                    ->withInput()
                    ->with([
                        'message' => trans('Akun anda tidak ditemukan'),
                        'type'    => "error"
                    ]);
            }
        } else {
            return redirect('/login')
                ->withInput()
                ->with([
                    'message' => trans('Akun anda tidak ditemukan'),
                    'type'    => "error"
                ]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login')
            ->withInput()
            ->with([
                'message' => trans('Berhasil Keluar'),
                'type'    => "success"
            ]);
    }
}
