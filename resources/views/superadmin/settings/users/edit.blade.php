<x-layouts.app :title="'Edit User: ' . $user->name">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('app.users.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2 text-sm font-medium">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar User
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-yellow-50">
                <h2 class="text-lg font-bold text-gray-800">Edit User</h2>
                <p class="text-xs text-gray-500">Perbarui informasi akun dan hak akses.</p>
            </div>

            <form action="{{ route('app.users.update', $user->id) }}" method="POST" class="p-6 space-y-6">
                @csrf @method('PUT')
                
                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-gray-50 cursor-not-allowed" readonly 
                        title="Email tidak disarankan diubah sembarangan">
                    <p class="text-[10px] text-gray-400 mt-1">*Email digunakan untuk login.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role / Jabatan</label>
                        <select name="role" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ $user->hasRole($role) ? 'selected' : '' }}>{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Password (Opsional) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru (Opsional)</label>
                        <input type="password" name="password" 
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Biarkan kosong jika tetap">
                        <p class="text-[10px] text-yellow-600 mt-1 font-bold">*Isi hanya jika ingin mereset password user.</p>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('app.users.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>