<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventtypeController extends Controller
{
    public function index()
    {
        $events = \DB::connection('mysql2')->table('eventtype')
        ->get();
        //return response()->json(Event::where('id','=','200')->get());
        return response()->json($events);
    }
}
