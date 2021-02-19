<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserPolicy extends Policy
{    
    public function view() { return $this->checkPermissions("user-view"); }
    public function viewSpecific() { return $this->checkPermissions("user-viewspecific"); }
    public function add() { return $this->checkPermissions("user-add"); }
    public function update() { return $this->checkPermissions("user-update"); }
    public function delete() { return $this->checkPermissions("user-delete"); }
}