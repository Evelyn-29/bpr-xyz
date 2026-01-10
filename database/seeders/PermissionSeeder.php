<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset Cached Roles and Permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // FORMAT BARU: Key = Nama Modul, Value = Array Permission
        $structure = [
            'Dashboard' => [
                'view_dashboard_admin',
                'view_dashboard_manager',
                'view_dashboard_direktur',
                'view_dashboard_superadmin',
            ],
            'Pengajuan' => [
                'view_pengajuan',
                'detail_pengajuan',
                'view_rekomendasi',
                'update_rekomendasi',
                'history_rekomendasi',
                'detail_rekomendasi',
                'view_persetujuan',
                'detail_persetujuan',
                'update_persetujuan',
            ],
            'Slik' => [
                'view_slik',
                'upload_slik',
            ],
            'Angsuran' => [
                'view_angsuran',
                'update_angsuran',
                'detail_angsuran',
                'reverse_angsuran',
            ],
            'Laporan' => [
                'view_laporan_persetujuan_kredit',
                'view_laporan_analisa_kredit',
                'view_laporan_monitoring_angsuran',
                'view_laporan_realisasi_pinjaman',
                'view_laporan_rekapitulasi',
            ],
            'Master Fasilitas Kredit' => [
                'view_master_produk',
                'create_master_produk',
                'edit_master_produk',
                'delete_master_produk',
            ],
            'Manajemen User' => [
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
            ],
            'Data Nasabah' => [
                'view_nasabah',
                'edit_nasabah',
                'delete_nasabah',
                'detail_nasabah',
            ],
            'System Management' => [
                'manage_roles',
            ]
        ];

        foreach ($structure as $module => $permissions) {
            foreach ($permissions as $permName) {
                // Kita simpan permission BESERTA nama modulnya
                Permission::firstOrCreate(
                    ['name' => $permName],
                    ['module_name' => $module] // Kolom tambahan tadi
                );
            }
        }

        // 3. DEFINE ROLES & ASSIGN PERMISSIONS

        // --- ROLE A: SUPERADMIN (IT / Developer) ---
        // Diberikan akses ke SEMUA permission yang ada
        $superadmin = Role::firstOrCreate(['name' => 'Superadmin']);
        $superadmin->givePermissionTo(Permission::all());

        // --- ROLE B: ADMIN (Teller / Staff) ---
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view_dashboard_admin',
            'view_pengajuan',
            'detail_pengajuan',
            'view_slik',
            'upload_slik',
            'view_angsuran',
            'update_angsuran',
            'view_laporan_persetujuan_kredit',
            'view_laporan_analisa_kredit',
            'view_laporan_monitoring_angsuran'
        ]);

        // --- ROLE C: MANAGER (Kepala Cabang / AO Head) ---
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->givePermissionTo([
            'view_dashboard_manager',
            'view_rekomendasi',
            'update_rekomendasi',
            'history_rekomendasi',
            'detail_rekomendasi',
            'view_angsuran',
            'detail_angsuran',
            'reverse_angsuran',
            'view_laporan_persetujuan_kredit',
            'view_laporan_analisa_kredit',
            'view_laporan_monitoring_angsuran'
        ]);

        // --- ROLE D: DIREKTUR (Pemutus Kredit) ---
        $direktur = Role::firstOrCreate(['name' => 'Direktur']);
        $direktur->givePermissionTo([
            'view_dashboard_direktur',
            'view_persetujuan',
            'detail_persetujuan',
            'update_persetujuan',
            'view_angsuran',
            'detail_angsuran',
            'view_laporan_persetujuan_kredit',
            'view_laporan_analisa_kredit',
            'view_laporan_monitoring_angsuran',
            'view_laporan_realisasi_pinjaman',
            'view_laporan_rekapitulasi',
        ]);

        // --- ROLE E: NASABAH (User Biasa) ---
        // Nasabah biasanya tidak perlu permission eksplisit jika logic-nya dipisah,
        // tapi kita buatkan saja rolenya.
        $nasabah = Role::firstOrCreate(['name' => 'Nasabah']);
    }
}
