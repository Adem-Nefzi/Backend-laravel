<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient_Offers extends Model
{
    use HasFactory;
    protected $table = 'recipient_offers';

    protected $fillable = [
        'association_id',
        'user_id',
        'title',
        'description',
        'status',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
