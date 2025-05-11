<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Association extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, SoftDeletes;

    // Add these properties:
    protected $table = 'associations'; // Explicit table name

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'description',
        'category',
        'logo_url',
        'user_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at', // Add this to hidden
    ];
    protected $dates = ['deleted_at'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
