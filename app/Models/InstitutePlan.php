<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutePlan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean'
    ];
}
