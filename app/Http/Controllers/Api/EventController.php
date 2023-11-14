<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CancelMail;
use App\Mail\ConfirmMail;
use App\Mail\UnconfirmMail;
use App\Mail\CarRequestMail;
use App\Models\Booking;
use App\Models\Deposit;
use App\Models\Event;
use App\Models\User;
use Codedge\Fpdf\Fpdf\Fpdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $events = \DB::connection('mysql2')->table('event')
        ->join('eventholder','event.eventHolder_id', 'eventholder.id')
        ->join('event_venue','event.id','event_venue.event_id')
        ->join('venue','event_venue.venueList_id','venue.id')   
        ->join('user','event.bookingTakingById','user.id')  
        ->join('eventtype','event.eventTypeId','eventtype.id')   
        ->select('event.*','eventholder.name as eholderName','eventholder.phoneNumber as mobile','venue.name as venue_name','user.name as fullname','eventtype.name as event_type')    
        ->orderByDesc('event.id')->where('event.name','like','%'.$request->keyword.'%')->limit(50)->get();
        //return response()->json(Event::where('id','=','200')->get());
        return response()->json($events);
    }
    public function packages()
    {
        $packages = \DB::connection('mysql2')->table('event_packagge')
        ->join('packagge','event_packagge.packaggeList_id','packagge.id')
        ->select('event_packagge.*','packagge.name')
        ->get();
        //return response()->json(Event::where('id','=','200')->get());
        return response()->json($packages);
    }
    public function report(Request $request)
    {
        // $validateData = $request->validate([
        //     'from_date' => 'required',
        //     'to_date'=>'required'




        //    ]);

          $bookings = Booking::whereBetween('date', [$request->from_date, $request->to_date])
          ->where('confirm','1')
          ->orderBy('date')
          ->get();

        $this->fpdf = new Fpdf();
        $width_cell=array(10,20,32,95,55,25,25,25,20,30,219);
        $this->fpdf->AddPage();
        $this->fpdf->SetFont('Times', 'B', 11);
        $this->fpdf->SetFillColor(230,230,230);
        $this->fpdf->Cell(189,10,'Report from '.date('d-m-y', strtotime($request->from_date)).' to '.date('d-m-y', strtotime($request->to_date)),0,1,'C',true);
        $this->fpdf->Cell($width_cell[0], 10, 'Sr',1,0,'C',1);
        $this->fpdf->Cell($width_cell[1], 10,'Date',1,0,'C',1);
        $this->fpdf->Cell($width_cell[2], 10, 'User',1,0,'C',1);
        $this->fpdf->Cell($width_cell[3], 10, 'Destination',1,0,'C',1);
         $this->fpdf->Cell($width_cell[2], 10, 'Driver',1,1,'C',1);
        // $this->fpdf->Cell($width_cell[1], 10, 'Amount',1,0,'C');
        // $this->fpdf->Cell($width_cell[1], 10, 'Kilo',1,1,'C');
        $i=1;
        $this->fpdf->SetFont('Times', '', 10);
        foreach($bookings as $dat) {
            $this->fpdf->Cell($width_cell[0], 8, $i++,1,0,'R');
            $this->fpdf->Cell($width_cell[1], 8, date('d-m-y', strtotime($dat->date)),1,0);
            if($dat->user){
            $this->fpdf->Cell($width_cell[2], 8, $dat->user->name,1,0,'L');
        } else {
            $this->fpdf->Cell($width_cell[2], 8, 'resigned',1,0,'L');
        }
            $this->fpdf->Cell($width_cell[3], 8, $dat->destination,1,0,'L');
            $this->fpdf->Cell($width_cell[2], 8, $dat->driver->name,1,1,'L');
         //   $this->fpdf->Cell($width_cell[1], 8, $dat->kilo,1,1,'R');
         }
        // $this->fpdf->Cell($width_cell[4], 10, 'Fuel Kilo/Liter- '. $average_consume,1,0,'R');
        // $this->fpdf->Cell($width_cell[1], 10, $last_qty->qty,1,0,'R');
        // $this->fpdf->Cell($width_cell[1], 10, number_format($average, 2, '.', ''),1,0,'R');
        // $this->fpdf->Cell($width_cell[1], 10, $sum_amt,1,0,'R');
        // $this->fpdf->Cell($width_cell[1], 10, $kilo_total,1,1,'R');
        $this->fpdf->Output();

        exit;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deposit(Request $request,$id)
    {
        $validateData = $request->validate([
            'amount' => 'required',
            'currency' => 'required',
            'payment_type' => 'required',
           
           ]);

           $events = \DB::connection('mysql2')->table('event')           
        ->join('eventholder','event.eventHolder_id', 'eventholder.id')
        ->join('event_venue','event.id','event_venue.event_id')
        ->join('venue','event_venue.venueList_id','venue.id')   
        ->join('user','event.bookingTakingById','user.id')     
        ->select('event.*','eventholder.name as eholderName','eventholder.phoneNumber as mobile','venue.name as venue_name','user.name as fullname')   
        ->where('event.id',$id)      
        ->first();

        $payment = new Deposit();
        $payment->payment_date = Carbon::today();
        $payment->currency = $request->currency;
        $payment->payment_type = $request->payment_type;
        $payment->amount = $request->amount;
        $payment->note = $request->note;
        $payment->event_id = $id;
        $payment->name = $events->name;
        $payment->assistant_person = $events->fullname;
        $payment->mobile = $events->mobile;
        $payment->event_date = $events->date;
        $payment->event_time = $events->time;
        $payment->venue = $events->venue_name;
        $payment->numberOfPax = $events->numberOfPax;
        $payment->expPax = $events->expPax;
        $payment->user_id = Auth::user()->id;



        $payment->save();
          
            return response()->json('Success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::where('id',$id)->update([
            'destination'=> $request->destination,
            'date' => $request->date,
            'time'=> $request->time,
            'duation'=> $request->duation,
        ]);
        return response()->json('Success');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $booking=Booking::where('id',$id)->first();
        Booking::where('id',$id)->delete();
        Mail::to($booking->user->email)

        //   ->cc(['nyeinchan@inyalakehotel.com','aungkyawsi@inyalakehotel.com','purchasing@inyalakehotel.com'])
               ->send(new CancelMail($booking));
    }

    public function confirm(Request $request, $id)
    {
        $validateData = $request->validate([
            'driver_id' => 'required',



        ]);
        $booking = Booking::where('id',$id)->first();

        Booking::where('id', $id)->update([
            'confirm' => true,
            'driver_id' => $request->driver_id,
        ]);
        $driver = Driver::find($request->driver_id);


        Mail::to($booking->user->email)

                  //  ->cc(['nyeinchan@inyalakehotel.com','aungkyawsi@inyalakehotel.com','purchasing@inyalakehotel.com'])
                        ->send(new ConfirmMail($driver));

    }
    public function unconfirm($id)
    {
        $booking = Booking::where('id',$id)->first();

        Booking::where('id', $id)->update([
            'confirm' => false,
            'driver_id'=> Null
        ]);


        Mail::to($booking->user->email)

                 //   ->cc(['nyeinchan@inyalakehotel.com','aungkyawsi@inyalakehotel.com','purchasing@inyalakehotel.com'])
                        ->send(new UnconfirmMail());

    }
}
