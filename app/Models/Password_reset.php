<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Password_reset extends Model
{
    public function getUpdatedAtColumn() {
        return null;
    }
}