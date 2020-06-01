<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'abbreviation', 'institute_id'];

    public function institute()
    {
     return $this->belongsTo(Institute::class);
    }
}
