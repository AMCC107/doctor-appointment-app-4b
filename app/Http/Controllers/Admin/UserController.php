<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

       // Exclude non-table fields and hash password
       $user = User::create([
           'name' => $data['name'],
           'email' => $data['email'],
           'password' => bcrypt($data['password']),
           'id_number' => $data['id_number'],
           'phone' => $data['phone'],
           'address' => $data['address'],
       ]);
       $user->roles()->attach($data['role_id']);

       // Si se crea un paciente, redirigir al formulario de edición del paciente
       $role = Role::find($data['role_id']);
       if ($role && $role->name === 'Paciente') {
           $patient = $user->patient()->create([]);
           session()->flash('swal', 
           [
            'icon' => 'success',
            'title' => 'Usuario creado correctamente',
            'text' => 'El usuario ha sido creado exitosamente',
           ]);
           return redirect()->route('admin.patients.edit', $patient)->with('success', 'Usuario creado correctamente');
       }

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
        $roles=Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validationRules = [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
            'id_number' => 'required|string|min:5|max:255|regex:/^[A-za-z0-9]+$/|unique:users,id_number,' . $user->id,
            'phone' => 'required|digits_between:7,15',
            'address' => 'required|string|min:3|max:255',
            'role_id' => 'required|exists:roles,id',
        ];

        // Validar contraseña solo si está presente
        if ($request->filled('password')) {
            $validationRules['password'] = 'required|string|min:8|confirmed';
            $validationRules['password_confirmation'] = 'required|string|min:8|same:password';
        }

        $data = $request->validate($validationRules);
    
           // Exclude role_id from user update
           $user->update([
               'name' => $data['name'],
               'email' => $data['email'],
               'id_number' => $data['id_number'],
               'phone' => $data['phone'],
               'address' => $data['address'],
           ]);
           //Si el usuario quiere editar su contraseña, que lo guarde
           if ($request->filled('password')) {
            $user->password = bcrypt($data['password']);
            $user->save();
           }
           $user->roles()->sync($data['role_id']);

           session()->flash('swal', 
           [
            'icon' => 'success',
            'title' => 'Usuario actualizado correctamente',
            'text' => 'El usuario ha sido actualizado exitosamente',
           ]);
           return redirect()->route('admin.users.edit', $user->id)->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        // Autorizar la acción usando la Policy
        // Esto lanzará automáticamente un 403 si la policy retorna false
        $this->authorize('delete', $user);
    // No permitir que el usuario logueado se borre a sí mismo 
    if (Auth::id() === $user->id){
        abort(403,'No puedes borrar tu propio usuario');
    }
        //Eliminar roles asociados a un usuario
        $user->roles()->detach();

        //Eliminar el usuario
        $user->delete();
        session()->flash('swal', 
        [
            'icon' => 'success',
            'title' => 'Usuario eliminado correctamente',
            'text' => 'El usuario ha sido eliminado exitosamente',
        ]);
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente');
    }
}
