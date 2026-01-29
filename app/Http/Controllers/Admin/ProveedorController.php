<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Proveedor;
use Illuminate\Support\Facades\Auth;

class ProveedorController extends Controller
{
    private function validarAdmin()
    {
        $u = Auth::user();
        if (!$u) abort(403, 'Debes iniciar sesión.');
        if (($u->role ?? null) !== 'admin') abort(403, 'Acceso solo para administradores.');
        return $u;
    }

    public function index(Request $request)
    {
        $this->validarAdmin();

        $q = trim((string) $request->get('q', ''));

        $proveedores = Proveedor::query()
            ->when($q, function($query) use ($q){
                $query->where('nombre', 'like', "%{$q}%")
                      ->orWhere('contacto', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('whatsapp', 'like', "%{$q}%")
                      ->orWhere('pais', 'like', "%{$q}%")
                      ->orWhere('ciudad', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.proveedores.index', compact('proveedores', 'q'));
    }

    public function create()
    {
        $this->validarAdmin();
        return view('admin.proveedores.create');
    }

    public function store(Request $request)
    {
        $this->validarAdmin();

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'pais' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'notas' => 'nullable|string|max:5000',
        ]);

        Proveedor::create($data);

        return redirect()->route('admin.proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        $this->validarAdmin();
        return view('admin.proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $this->validarAdmin();

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'pais' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'notas' => 'nullable|string|max:5000',
        ]);

        $proveedor->update($data);

        return redirect()->route('admin.proveedores.index')
            ->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $this->validarAdmin();

        // Si está asociado a seguimientos, no eliminar
        if ($proveedor->seguimientos()->exists()) {
            return back()->with('error', 'No puedes eliminar este proveedor porque tiene seguimientos asociados.');
        }

        $proveedor->delete();

        return redirect()->route('admin.proveedores.index')
            ->with('success', 'Proveedor eliminado.');
    }
}
