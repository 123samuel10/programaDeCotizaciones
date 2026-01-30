<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = User::where('role', 'cliente')
            ->orderBy('name')
            ->get();

        return view('admin.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('admin.clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'empresa'  => 'nullable|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'pais'      => 'nullable|string|max:100',
'ciudad'    => 'nullable|string|max:120',
'direccion' => 'nullable|string|max:255',
'telefono'  => 'nullable|string|max:40',
'nit'       => 'nullable|string|max:60',

        ]);

        User::create([
            'name'     => $request->name,
            'empresa'  => $request->empresa,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'cliente',
            'pais'      => $request->pais,
'ciudad'    => $request->ciudad,
'direccion' => $request->direccion,
'telefono'  => $request->telefono,
'nit'       => $request->nit,

        ]);

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(User $cliente)
    {
        if ($cliente->role !== 'cliente') abort(404);

        return view('admin.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, User $cliente)
    {
        if ($cliente->role !== 'cliente') abort(404);

        $request->validate([
            'name'    => 'required|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $cliente->id,
            'password'=> 'nullable|min:6',
            'pais'      => 'nullable|string|max:100',
'ciudad'    => 'nullable|string|max:120',
'direccion' => 'nullable|string|max:255',
'telefono'  => 'nullable|string|max:40',
'nit'       => 'nullable|string|max:60',

        ]);

        $data = [
            'name'    => $request->name,
            'empresa' => $request->empresa,
            'email'   => $request->email,
            'pais'      => $request->pais,
'ciudad'    => $request->ciudad,
'direccion' => $request->direccion,
'telefono'  => $request->telefono,
'nit'       => $request->nit,

        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $cliente->update($data);

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente actualizado.');
    }

    public function destroy(User $cliente)
    {
        if ($cliente->role !== 'cliente') abort(404);

        // evitar borrar si tiene cotizaciones
        if ($cliente->cotizaciones()->exists()) {
            return back()->with('error', 'No puedes eliminar este cliente porque tiene cotizaciones.');
        }

        $cliente->delete();

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
