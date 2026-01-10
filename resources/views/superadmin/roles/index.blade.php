<x-layouts.app title="Role & Permission">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-gray-800">Pengaturan Role & Permissions</h1>
    </x-slot>
    <div class="bg-white p-6 rounded-2xl shadow-sm">
        {{-- Form Tambah Role Simpel --}}
        <form action="{{ route('app.roles.store') }}" method="POST" class="mb-6 flex gap-2">
            @csrf
            <input type="text" name="name" placeholder="Nama Role Baru (ex: Supervisor)"
                class="rounded-lg shadow-sm border border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah Role</button>
        </form>

        <table class="w-full text-sm text-left text-gray-600">
            <thead class="bg-gray-50 text-gray-700 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Permission (Jumlah)</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr class="border-b">
                        <td class="px-6 py-3 font-bold">{{ $role->name }}</td>
                        <td class="px-6 py-3">
                            <span class="bg-gray-200 text-xs px-2 py-1 rounded">{{ $role->permissions->count() }}
                                Akses</span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <a href="{{ route('app.roles.edit', $role->id) }}"
                                class="inline-flex items-center justify-center w-8 h-8 bg-white border border-blue-200 text-blue-500 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition shadow-sm"
                                title="Edit">
                                <i class="fa-solid fa-pencil"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-100">
            {{ $roles->links() }}
        </div>
    </div>
</x-layouts.app>
