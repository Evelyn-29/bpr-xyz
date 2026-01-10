<x-layouts.app title="Edit Permission">

    <x-slot name="header">
        <a href="{{ route('app.roles.index') }}" class="text-gray-500 hover:text-gray-700 text-lg font-bold">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar Role
        </a>
    </x-slot>

    <form action="{{ route('app.roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Header Data Role --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-sm text-gray-500 font-medium uppercase tracking-wider">Mengedit Role</h2>
                <h1 class="text-3xl font-bold text-gray-800 mt-1">{{ $role->name }}</h1>
            </div>
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>

        {{-- GRID MODULES --}}
        <div class="grid grid-cols-1 gap-6">

            @foreach ($modules as $moduleName => $permissions)
                @php
                    // PERSIAPAN DATA UNTUK ALPINE JS
                    // 1. Ambil semua 'name' permission di modul ini
                    $allModulePermissions = $permissions->pluck('name')->toArray();

                    // 2. Cari mana dari permission modul ini yang SUDAH dimiliki role (Intersection)
                    // Kita gunakan array_values untuk reset index agar jadi array JS yang valid (['a','b'], bukan {0:'a', 2:'b'})
                    $roleHasPermissions = array_values(array_intersect($allModulePermissions, $rolePermissions));
                @endphp

                {{-- KARTU PER MODUL (Scope Alpine JS dimulai di sini) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full"
                    x-data="{
                        // Array yang menampung apa yang dicentang
                        selected: {{ json_encode($roleHasPermissions) }},
                    
                        // Array referensi semua permission di modul ini
                        all: {{ json_encode($allModulePermissions) }},
                    
                        // Getter: Cek apakah semua sudah terpilih
                        get allSelected() {
                            return this.selected.length === this.all.length;
                        },
                    
                        // Action: Toggle Select All
                        toggleAll() {
                            if (this.allSelected) {
                                this.selected = [];
                            } else {
                                this.selected = [...this.all];
                            }
                        }
                    }">

                    {{-- HEADER MODUL --}}
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg uppercase tracking-wide">
                                {{ $moduleName }}
                            </h3>
                            {{-- Counter Badge (Reaktif via Alpine) --}}
                            <span class="text-xs font-semibold px-2 py-0.5 rounded"
                                :class="selected.length > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-500'"
                                x-text="selected.length + ' / ' + all.length + ' Akses'">
                            </span>
                        </div>

                        {{-- TOGGLE SELECT ALL --}}
                        <label
                            class="flex items-center space-x-2 cursor-pointer select-none bg-white border border-gray-300 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition">
                            <input type="checkbox"
                                class="form-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                x-on:click="toggleAll()" x-bind:checked="allSelected">
                            <span class="text-xs font-bold text-gray-600 uppercase">Pilih Semua</span>
                        </label>
                    </div>

                    {{-- BODY CHECKBOXES --}}
                    <div class="p-6 grid grid-cols-1 gap-3 flex-1">
                        @foreach ($permissions as $perm)
                            <label
                                class="flex items-start space-x-3 p-2 rounded-lg transition cursor-pointer group border"
                                :class="selected.includes('{{ $perm->name }}') ? 'bg-blue-50 border-blue-200' :
                                    'bg-white border-transparent hover:bg-gray-50 hover:border-gray-200'">

                                {{-- INPUT UTAMA (x-model menghubungkan checkbox ini dengan array 'selected') --}}
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                    x-model="selected"
                                    class="form-checkbox w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mt-0.5">

                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-700 group-hover:text-blue-700"
                                        :class="selected.includes('{{ $perm->name }}') ? 'text-blue-800' : ''">
                                        {{ ucwords(str_replace('_', ' ', $perm->name)) }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-mono mt-0.5">
                                        {{ $perm->name }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>

        {{-- Floating Action Button (Optional / Tambahan di bawah) --}}
        <div class="mt-8 flex justify-end">
            <button type="submit"
                class="bg-gray-800 text-white px-8 py-4 rounded-xl font-bold hover:bg-black transition text-lg shadow-xl">
                Simpan Konfigurasi
            </button>
        </div>
    </form>
</x-layouts.app>
