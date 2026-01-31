<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Group;

class UserController extends Controller
{
    protected $title = 'Manajemen User';
    protected $route = 'admin.users';

    public function index()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'User Management'],
            ['name' => 'Data User', 'active' => true],
        ];
        $data['title'] = $this->title;
        $data['route'] = $this->route;

        return view('admin.users.index', $data);
    }

    public function grid(Request $request)
    {
        // Get all users with their groups
        $users = User::with('group')->get();

        $data = $users->map(function ($user, $index) {
            return [
                'no' => $index + 1,
                'id' => encode_id($user->id),
                'name' => $user->name,
                'role' => $user->group->name ?? 'N/A',
                'username' => $user->username,
                'email' => $user->email,
                'created_at' => dateTime($user->created_at),
                'action' => '<div class="d-flex gap-1">
                    <a href="' . route('admin.users.update', encode_id($user->id)) . '" class="btn btn-sm btn-primary"><i class="mdi mdi-pencil"></i></a>
                    <a href="#" data-href="' . route('admin.users.delete', encode_id($user->id)) . '" class="btn btn-sm btn-danger remove_data"><i class="mdi mdi-delete"></i></a>
                </div>'
            ];
        })->toArray();

        return response()->json($data);
    }

    public function update($id = null)
    {
        $data['breadcrumbs'] = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'User Management'],
            ['name' => 'Data User', 'url' => route('admin.users.index')],
            ['name' => $id ? 'Edit User' : 'Tambah User', 'active' => true],
        ];

        $keyId = $id ? decode_id($id) : null;
        $data['title'] = $id ? 'Edit User' : 'Tambah User';
        $data['route'] = $this->route;
        $data['keyId'] = $id;
        $data['item'] = $keyId ? User::find($keyId) : null;
        $data['group'] = Group::where('MsGroupId', '!=', 1)->get();

        return view('admin.users.form', $data);
    }

    public function store(Request $request)
    {
        $keyId = decode_id($request->secure_id);

        if ($keyId) {
            $request->validate([
                'email' => 'required|unique:users,email,' . $keyId . '|email',
                'name' => 'required|max:50',
                'group' => 'required',
                'username' => 'required|unique:users,username,' . $keyId,
                'password' => 'nullable|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            ]);

            $user = User::find($keyId);
            $user->email = $request->email;
            $user->username = $request->username;
            $user->ms_group_id = decode_id($request->group);
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->name = $request->name;
            $user->save();
        } else {
            $request->validate([
                'email' => 'required|unique:users,email|email',
                'name' => 'required|max:50',
                'group' => 'required',
                'username' => 'required|unique:users,username',
                'password' => 'required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            ]);

            $user = new User;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->ms_group_id = decode_id($request->group);
            $user->password = Hash::make($request->password);
            $user->name = $request->name;
            $user->save();
        }

        return redirect()->route('admin.users.index')->with([
            'message' => 'Berhasil update data',
            'type' => 'success',
        ]);
    }

    public function delete($id)
    {
        $keyId = decode_id($id);
        User::where('id', $keyId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus data',
            'type' => 'success'
        ]);
    }

    public function forcelogin($id = null)
    {
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ditemukan',
                'type' => 'error'
            ]);
        }

        $id = decode_id($id);
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ditemukan',
                'type' => 'error'
            ]);
        }

        if (Auth::loginUsingId($id)) {
            $session = [
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'currYear' => date('Y'),
                'group_id' => @$user->ms_group_id,
                'group_alias' => @$user->group->alias,
                'group_name' => @$user->group->name,
            ];
            session($session);

            return response()->json([
                'status' => true,
                'message' => 'Selamat datang kembali',
                'type' => 'success'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Maaf Terjadi Kesalahan',
            'type' => 'error'
        ]);
    }
}
