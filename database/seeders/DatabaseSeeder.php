<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = \App\Models\Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $pimpinanRole = \App\Models\Role::create(['name' => 'Pimpinan', 'slug' => 'pimpinan']);
        $staffRole = \App\Models\Role::create(['name' => 'Staff', 'slug' => 'staff']);

        // 2. Create Permissions
        $permissions = [
            'create_surat', 'edit_surat', 'delete_surat', 'approve_surat', 
            'disposisi_surat', 'view_report', 'manage_user'
        ];

        foreach ($permissions as $perm) {
            $permission = \App\Models\Permission::create([
                'name' => ucwords(str_replace('_', ' ', $perm)),
                'slug' => $perm
            ]);

            // Assign all but approve_surat to admin
            $adminRole->permissions()->attach($permission);

            if (in_array($perm, ['approve_surat', 'disposisi_surat', 'view_report'])) {
                $pimpinanRole->permissions()->attach($permission);
            }

            if (in_array($perm, ['create_surat', 'edit_surat'])) {
                $staffRole->permissions()->attach($permission);
            }
        }

        // 3. Create Users
        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@blu.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach($adminRole);

        $pimpinan = User::factory()->create([
            'name' => 'Direktur Utama',
            'email' => 'pimpinan@blu.com',
            'password' => bcrypt('password'),
        ]);
        $pimpinan->roles()->attach($pimpinanRole);

        $staff = User::factory()->create([
            'name' => 'Staff Administrasi',
            'email' => 'staff@blu.com',
            'password' => bcrypt('password'),
        ]);
        $staff->roles()->attach($staffRole);

        // 4. Seed Extra 50 Users with random roles
        $users = User::factory(50)->create();
        $roles = [$adminRole->id, $pimpinanRole->id, $staffRole->id];
        foreach ($users as $user) {
            $user->roles()->attach(fake()->randomElement($roles));
        }

        // 5. Seed 200 Surat Masuk
        $suratMasukList = \App\Models\SuratMasuk::factory(200)->create();

        // 6. Seed 200 Surat Keluar
        \App\Models\SuratKeluar::factory(200)->create();

        // 7. Seed 200 Disposisi
        $allUsers = User::all();
        foreach (range(1, 200) as $index) {
            \App\Models\Disposisi::create([
                'surat_masuk_id' => $suratMasukList->random()->id,
                'pengirim_id' => $allUsers->random()->id,
                'penerima_id' => $allUsers->random()->id,
                'instruksi' => fake()->paragraph(),
                'catatan' => fake()->sentence(),
                'status' => fake()->randomElement(['pending', 'completed']),
            ]);
        }
    }
}
