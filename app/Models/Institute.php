<?php

namespace App\Models;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Institute extends Model
{
    protected $fillable = ['name', 'board', 'logo'];
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
}
