<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    // Menampilkan list Role
    public function index()
    {
        $roles = Role::with('permissions')->latest()->paginate(20);
        return view('superadmin.roles.index', compact('roles'));
    }

    // Form Edit/Create (Kita jadikan satu halaman biar praktis)
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        // Ambil semua permission, kita group biar rapi di view (opsional)
        // $permissions = Permission::all();

        // Ambil ID permission yang sudah dimiliki role ini
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $modules = Permission::all()->groupBy('module_name');

        return view('superadmin.roles.edit', compact('role', 'rolePermissions', 'modules'));
    }

    // Simpan Perubahan Permission
    public function update(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'array'
        ]);

        $role = Role::findOrFail($id);

        // Sync (Hapus yang lama, ganti yang baru dicentang)
        $role->syncPermissions($request->permissions);

        return redirect()->route('app.roles.index')->with('success', 'Permission berhasil diupdate!');
    }

    // Tambah Role Baru
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles,name']);
        $role = Role::create(['name' => $request->name]);
        return redirect()->route('app.roles.edit', $role->id)
            ->with('success', 'Role berhasil dibuat! Silakan atur hak aksesnya di bawah ini.');
    }
}
