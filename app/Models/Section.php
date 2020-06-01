<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'institute_id'];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }
}
