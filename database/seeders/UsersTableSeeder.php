<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $token = Hash::make(Str::random(59));
        $user = new User;
        $user->name = "Admin";
        $user->email = "admin123@gmail.com";
        $user->password = Hash::make("admin123");
        $user->api_token = $token;
        $user->save();

        $role = Role::where("slug","superadmin")->first()->id;
        $user->roles()->attach($role);
        $user->save();
    }
}
