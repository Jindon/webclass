<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iclass extends Model
{
    protected $fillable = ['name', 'description', 'institute_id'];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function sections()
    {
        return $this->hasMany(IclassSection::class)->with('section');
    }
}
