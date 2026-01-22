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
        ]);

        User::create([
            'name'     => $request->name,
            'empresa'  => $request->empresa,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'cliente',
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
        ]);

        $data = [
            'name'    => $request->name,
            'empresa' => $request->empresa,
            'email'   => $request->email,
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
