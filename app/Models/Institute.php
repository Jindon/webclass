<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Institute extends Model
{
    protected $fillable = ['name', 'board', 'logo', 'subdomain'];
    protected $casts = [
        'status' => 'boolean'
    ];

    public function path()
    {
        return "/institutes/{$this->id}";
    }

    public function addLogo($file)
    {
        $original_ext = $file->extension();
        $file_name = Str::snake($this->name) . '_' . Carbon::now()->timestamp . ".{$original_ext}";
        $file->storeAs('logos', $file_name, 'public');

        $this->logo = $file_name;
        $this->save();
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function plan()
    {
        return $this->hasOne(InstitutePlan::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function iclasses()
    {
        return $this->hasMany(Iclass::class)->with(['sections']);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
