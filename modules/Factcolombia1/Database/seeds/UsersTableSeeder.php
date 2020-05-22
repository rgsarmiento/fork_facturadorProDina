<?php

use Illuminate\Database\Seeder;
use App\Models\System\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@factura.com',
            'password' => bcrypt('123456')
        ]);
    }
}
