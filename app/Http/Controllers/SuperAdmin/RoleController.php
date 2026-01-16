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

    public function destroy($id)
    {
        $role = Role::withCount('users')->findOrFail($id);

        // 1. KEAMANAN LEVEL 1: Jangan hapus Superadmin
        if (in_array($role->name, ['Superadmin'])) {
            return back()->with('error', 'Role Inti (Superadmin) tidak dapat dihapus demi keamanan sistem.');
        }

        // 2. KEAMANAN LEVEL 2: Cek apakah masih ada user yang pakai role ini
        if ($role->users_count > 0) {
            return back()->with('error', "Gagal menghapus! Masih ada {$role->users_count} user yang menggunakan role ini. Silakan ganti role user tersebut terlebih dahulu.");
        }

        try {
            $role->delete();
            return redirect()->route('app.roles.index')->with('success', 'Role berhasil dihapus permanen.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
