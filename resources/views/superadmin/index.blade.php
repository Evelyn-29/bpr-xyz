<x-layouts.app title="Dashboard Sistem">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                    <i class="fa-solid fa-users-gear text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Role</p>
                    <h3 class="text-2xl font-bold">{{ \Spatie\Permission\Models\Role::count() }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 text-green-600 rounded-lg">
                    <i class="fa-solid fa-key text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Permission</p>
                    <h3 class="text-2xl font-bold">{{ \Spatie\Permission\Models\Permission::count() }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-100 text-purple-600 rounded-lg">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total User</p>
                    <h3 class="text-2xl font-bold">{{ \App\Models\User::count() }}</h3>
                </div>
            </div>
        </div>
    </div>
</x-layouts.superadmin>
