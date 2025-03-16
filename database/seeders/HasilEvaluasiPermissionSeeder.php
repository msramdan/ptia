<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HasilEvaluasiPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar permission untuk Hasil Evaluasi
        $permissions = ['hasil evaluasi view'];

        // Buat atau pastikan permission ada di database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Cari atau buat role "admin"
        $role = Role::firstOrCreate(['name' => 'admin']);

        // Berikan semua permission Hasil Evaluasi ke role "admin"
        $role->syncPermissions($permissions);
    }
}
