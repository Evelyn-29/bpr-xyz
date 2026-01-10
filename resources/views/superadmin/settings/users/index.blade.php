<x-layouts.app title="Manajemen Pengguna">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Manajemen Pengguna System</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- HEADER: SEARCH & ACTION --}}
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Daftar User</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola akun staff, admin, dan hak akses.</p>
            </div>

            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                {{-- SEARCH --}}
                <form action="{{ route('app.users.index') }}" method="GET" class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari Nama / Email..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('app.users.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition">
                            <i class="fa-solid fa-times-circle"></i>
                        </a>
                    @endif
                </form>

                {{-- ADD BUTTON --}}
                @can('create_users')
                <a href="{{ route('app.users.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center justify-center gap-2 shadow-sm transition transform active:scale-95 text-sm font-medium whitespace-nowrap">
                    <i class="fa-solid fa-user-plus"></i> 
                    Tambah User
                </a>
                @endcan
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">Nama User</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role (Jabatan)</th>
                        <th class="px-6 py-4">Terdaftar</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- Nama --}}
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $user->name }}</div>
                        </td>

                        {{-- Email --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-envelope text-gray-400"></i>
                                <span>{{ $user->email }}</span>
                            </div>
                        </td>

                        {{-- Role Badge --}}
                        <td class="px-6 py-4">
                            @foreach($user->roles as $role)
                                @php
                                    $color = match($role->name) {
                                        'Superadmin' => 'bg-red-100 text-red-700',
                                        'Direktur'   => 'bg-orange-100 text-orange-700',
                                        'Manager'    => 'bg-blue-100 text-blue-700',
                                        'Admin'      => 'bg-green-100 text-green-700',
                                        'Nasabah'    => 'bg-gray-100 text-gray-600',
                                        default      => 'bg-gray-100 text-gray-600'
                                    };
                                @endphp
                                <span class="{{ $color }} px-2.5 py-1 rounded-full text-xs font-bold border border-opacity-20 border-current">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-6 py-4 text-xs">
                            {{ $user->created_at->format('d M Y') }}
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @can('edit_users')
                                <a href="{{ route('app.users.edit', $user->id) }}" 
                                   class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition shadow-sm" title="Edit User">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @endcan
                                
                                @can('delete_users')
                                    @if(auth()->id() !== $user->id)
                                    <form action="{{ route('app.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user {{ $user->name }}? Akses login mereka akan hilang permanen.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-600 transition shadow-sm" title="Hapus User">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fa-solid fa-users-slash text-2xl text-gray-400"></i>
                                </div>
                                <p class="font-medium">Data user tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="p-4 border-t border-gray-100 bg-white">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.app>