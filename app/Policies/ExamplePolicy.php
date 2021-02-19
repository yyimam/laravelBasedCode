<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class ExamplePolicy extends Policy{
    
    public function view() { return $this->checkPermissions("example-view"); }
    public function viewSpecific() { return $this->checkPermissions("example-viewSpecific"); }
    public function add() { return $this->checkPermissions("example-add"); }
    public function update() { return $this->checkPermissions("example-update"); }
    public function delete() { return $this->checkPermissions("example-delete"); }
}