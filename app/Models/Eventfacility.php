<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eventfacility extends Model
{
    use HasFactory;
    protected $fillable = [
        'eventtype_id',
        'facility_id'
 
     ];
}
