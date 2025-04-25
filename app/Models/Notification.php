<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
use HasFactory;

protected $fillable = [
'user_id',
'title',
'message',
'type',
'is_read',
'related_offer_id',
'related_application_id',
'related_message_id',
];

protected $casts = [
'is_read' => 'boolean',
];

// Relationships
public function user()
{
return $this->belongsTo(User::class);
}

public function relatedOffer()
{
return $this->belongsTo(DonationOffer::class, 'related_offer_id');
}

public function relatedApplication()
{
return $this->belongsTo(Application::class, 'related_application_id');
}

public function relatedMessage()
{
return $this->belongsTo(Message::class, 'related_message_id');
}
}
