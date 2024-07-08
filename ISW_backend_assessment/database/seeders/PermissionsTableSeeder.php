<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $existing = DB::table('permissions')->where('code_name', 'view_permissions')->get();
        if ($existing->count() == 0) {
            DB::table('permissions')->insert([
                'code_name' => 'view_permissions',
                'display_name' => 'view permissions',
                'uuid' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        $existing = DB::table('permissions')->where('code_name', 'create_role')->get();
        if ($existing->count() == 0) {
            DB::table('permissions')->insert([
                'code_name' => 'create_role',
                'display_name' => 'Create Role',
                'uuid' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        $existing = DB::table('permissions')->where('code_name', 'view_role')->get();
        if ($existing->count() == 0) {
            DB::table('permissions')->insert([
                'code_name' => 'view_role',
                'display_name' => 'View Role',
                'uuid' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        $existing = DB::table('permissions')->where('code_name', 'create_post')->get();
        if ($existing->count() == 0) {
            DB::table('permissions')->insert([
                'code_name' => 'create_post',
                'display_name' => 'Create Post',
                'uuid' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
