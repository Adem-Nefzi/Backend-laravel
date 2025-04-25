<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'applicant_id',
        'message',
        'status',
        'response_message',
    ];

    // Relationships
    public function offer()
    {
        return $this->belongsTo(DonationOffer::class, 'offer_id');
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function donation()
    {
        return $this->hasOne(Donation::class, 'application_id');
    }
}
