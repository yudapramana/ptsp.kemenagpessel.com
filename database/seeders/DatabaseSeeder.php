<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(MasterDisposisiSeeder::class);
        $this->call(LayananTableSeeder::class);
        $this->call(SyaratLayananTableSeeder::class);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
