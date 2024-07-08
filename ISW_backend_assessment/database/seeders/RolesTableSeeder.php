<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $existing_admin = DB::table('roles')->where('title', 'admin')->get();
        if($existing_admin->count() == 0){
            DB::table('roles')->insert([
                'title' => 'admin',
                'status' => 'active',
                'uuid' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
