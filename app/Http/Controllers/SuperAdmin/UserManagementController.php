<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Nasabah');
            })
            ->latest();

        // Fitur Pencarian (Nama / Email)
        $query->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        });

        // Filter Role (Opsional, buat jaga-jaga)
        $query->when($request->role, function ($q) use ($request) {
            $q->role($request->role);
        });

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::pluck('name', 'name'); // Untuk filter dropdown jika nanti mau ditambah

        return view('superadmin.settings.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'Nasabah')
            ->pluck('name', 'name');
        return view('superadmin.settings.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Buat User
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                // 2. Assign Role
                $user->assignRole($request->role);
            });

            return redirect()->route('app.users.index')->with('success', 'User baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat user: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::where('name', '!=', 'Nasabah')
            ->pluck('name', 'name');
        return view('superadmin.settings.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|min:6', // Nullable: Kalau kosong berarti gak mau ganti password
            'role'     => 'required|exists:roles,name',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                // 1. Update Data Dasar
                $dataToUpdate = [
                    'name'  => $request->name,
                    'email' => $request->email,
                ];

                // Jika password diisi, update password hash
                if ($request->filled('password')) {
                    $dataToUpdate['password'] = Hash::make($request->password);
                }

                $user->update($dataToUpdate);

                // 2. Sync Role (Ganti role lama dengan yang baru)
                $user->syncRoles([$request->role]);
            });

            return redirect()->route('app.users.index')->with('success', 'Data user berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update user: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        if ($id == auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri saat sedang login.');
        }

        $user = User::findOrFail($id);

        // Hapus Nasabah Profile jika ada (Optional, tergantung kebijakan)
        // $user->nasabahProfile()->delete(); 

        $user->delete();

        return redirect()->route('app.users.index')->with('success', 'User berhasil dihapus.');
    }
}
