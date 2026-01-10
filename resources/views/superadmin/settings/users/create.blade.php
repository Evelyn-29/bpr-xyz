<x-layouts.app title="Tambah User Baru">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('app.users.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2 text-sm font-medium">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar User
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800">Form Tambah User</h2>
                <p class="text-xs text-gray-500">Buat akun baru untuk staff atau admin.</p>
            </div>

            <form action="{{ route('app.users.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" placeholder="Contoh: Budi Santoso">
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" placeholder="user@company.com">
                    @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role / Jabatan</label>
                        <select name="role" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">*Menentukan hak akses menu.</p>
                        @error('role') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" required
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="********">
                        <p class="text-[10px] text-gray-400 mt-1">*Minimal 6 karakter.</p>
                        @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('app.users.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium shadow-md">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>