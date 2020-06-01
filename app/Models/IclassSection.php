<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IclassSection extends Model
{
    protected $fillable = ['institute_id', 'iclass_id', 'section_id'];

    public function iclass()
    {
        return $this->belongsTo(Iclass::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
