<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Eventfacility;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 



class FacilityController extends Controller
{
    public function index()
    {
        $facilities = \DB::table('facilities')
        ->get();
        
        return response()->json($facilities);
    }
    public function store(Request $request)
    {
        
        $facility = new Facility();
       $facility->facility_name = $request->facility_name;



        $facility->save();
        return response()->json('Success');
    }
    public function show(request $request,$id)
    {

        $facilities = \DB::table('eventfacilities')
        ->join('facilities','eventfacilities.facility_id','facilities.id')
        ->select('facilities.*')
        ->where('eventfacilities.eventtype_id',$id)
        ->get();
        
        return response()->json($facilities);
    }
    public function Link(request $request,$id)
    {
        // $validateData = $request->validate([
        //     'facility_id' => Rule::unique('eventfacilities')->where(function ($query) {
        //         return $query->where('eventtype_id', $id)
        //             ->where('facility_id', $request->facility_id);
        //         })       
           


        //    ]);

        //     $validatedData = $request->validate([
        //     'item_id' => 'required|unique:req_records,item_id,' . $request->item_id . ',id,req_id,' . $req_id,

        // ],
        //     [
        //         'item_id.unique' => 'You already requested this item with same req_id',
        //     ]);
           $verify =  \DB::table('eventfacilities')
           ->where('eventtype_id',$id)
           ->where('facility_id',$request->id)
           ->first();
           if(!$verify){
        $facilitylink = new Eventfacility();
        $facilitylink->eventtype_id = $id;
        $facilitylink->facility_id = $request->id;
        $facilitylink->save();
        
        return response()->json('Success');
    }
    }
    public function deleteLink(request $request, $id)
    {
       
        \DB::table('eventfacilities')
        ->where('eventtype_id',$id)
        ->where('facility_id',$request->facility_id)
        ->delete();

      
        
        return response()->json('$delData');
    }
}
