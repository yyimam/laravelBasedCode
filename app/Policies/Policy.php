<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class Policy
{    
    public function checkPermissions($policy_name) : bool
    {
        $current_user = User::where('id',Auth::user()->id)->first();
        $current_role_id = $current_user->roles()->first()->id;
        $current_permissions = Role::where('id', $current_role_id)->with("permissions")->first()->permissions->pluck("slug");
        
        foreach($current_permissions as $perm){
            if ($perm == $policy_name) {
                return true;
            }
        }
        return false;
    }
}