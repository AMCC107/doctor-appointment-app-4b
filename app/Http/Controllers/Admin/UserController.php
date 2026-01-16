<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles=Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
       $data=$request->validate([
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|string|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8|same:password',
        'id_number' => 'required|string|min:5|max:255|regex:/^[A-za-z0-9]+$/|unique:users',
        'phone' => 'required|digits_between:7,15',
        'address' => 'required|string|min:3|max:255',
        'role_id' => 'required|exists:roles,id',
       ]);

       $user=User::create($data);
       $user->roles()->attach($request->role_id);
       session()->flash('swal', 
       [
        'icon' => 'success',
        'title' => 'Usuario creado correctamente',
        'text' => 'El usuario ha sido creado exitosamente',
       ]);
       return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {

    }

    public function destroy(User $user)
    {

    }
}
