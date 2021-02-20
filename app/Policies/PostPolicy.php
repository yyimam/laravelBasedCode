<?php

namespace App\Policies;
use Illuminate\Support\Facades\Auth;

class PostPolicy extends Policy
{
    public function view() { return $this->checkPermissions("post-view"); }
    public function viewSpecific() { return $this->checkPermissions("post-viewspecific"); }
    public function add() { return $this->checkPermissions("post-add"); }
    public function update() { return $this->checkPermissions("post-update"); }
    public function delete() { return $this->checkPermissions("post-delete"); }
}