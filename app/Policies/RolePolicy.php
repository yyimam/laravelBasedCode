<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class RolePolicy extends Policy
{    
    public function view() { return $this->checkPermissions("role-view"); }
    public function viewSpecific() { return $this->checkPermissions("role-viewspecific"); }
    public function add() { return $this->checkPermissions("role-add"); }
    public function update() { return $this->checkPermissions("role-update"); }
    public function delete() { return $this->checkPermissions("role-delete"); }
}