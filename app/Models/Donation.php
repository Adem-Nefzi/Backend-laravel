<?php

namespace Database\Migrations;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'donor_id',
        'recipient_id',
        'application_id',
        'handover_date',
        'status',
        'feedback_donor',
        'feedback_recipient',
    ];

    // Relationships
    public function offer()
    {
        return $this->belongsTo(DonationOffer::class, 'offer_id');
    }

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}
