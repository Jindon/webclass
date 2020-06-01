<?php

namespace App\Models;

use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'institute_id', 'name', 'email', 'country_code', 'phone', 'password', 'status'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token, $this->institute->subdomain));
    }

    public function updatePassword($password)
    {
        $this->password = Hash::make($password);
        $this->save();
    }

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }
}
