<?php

namespace App\Models;

use Illuminate\Console\Application;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'creator_id',
        'type',
        'description',
        'quantity',
        'condition',
        'expiry_date',
        'location',
        'status',
        'images_urls',
    ];

    protected $casts = [
        'images_urls' => 'array',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'offer_id');
    }

    public function donation()
    {
        return $this->hasOne(Donation::class, 'offer_id');
    }
}
