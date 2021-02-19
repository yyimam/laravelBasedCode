<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role;
        $role->name = "Super Admin";
        $role->slug = "superadmin";
        $role->save();

        $listOfPermission = array("User View","User ViewSpecific","User Add","User Update","User Delete","Post View","Post ViewSpecific","Post Add","Post Update","Post Delete","Role View","Role ViewSpecific","Role Add","Role Update","Role Delete");

        foreach ($listOfPermission as $Permission) {
            $permissions = new Permission;
            $permissions->name = $Permission;
            $permissions->slug = strtolower(str_replace(" ","-", $Permission));
            $permissions->save();

            $role->permissions()->attach($permissions->id);
            $role->save();
        }
    }
}
