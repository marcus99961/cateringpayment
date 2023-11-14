<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;
    protected $fillable = [
        'event_id',
        'name',
        'assistant_person',
        'mobile',
        'event_date',
        'event_time',
        'venue',
        'numberOfPax',
        'expPax',
        'deposit_date',
        'amount',
        'currency',
        'note',
        'payment_type'

    ];
}
