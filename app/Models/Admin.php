<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = ['institute_id', 'name', 'email', 'country_code', 'phone', 'password', 'status'];
    protected $hidden = ['password'];
}
