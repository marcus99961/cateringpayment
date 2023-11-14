<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;
use NumberFormatter;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Models\Pdf;

class PaymentController extends Controller
{
    protected $pdf;

    public function __construct(\App\Models\Pdf $pdf)
    {
        $this->pdf = $pdf;
        return $this->middleware('auth:api');
    }
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
        $payments = Deposit::where('name','like','%'.$request->keyword.'%')->get();
        //return response()->json(Event::where('id','=','200')->get());
        
        return response()->json($payments);
    }
    public function receipt(string $id)
    {
        $payment = Deposit::where('id',$id)->first();
        $events = \DB::connection('mysql2')->table('event')
        ->join('eventholder','event.eventHolder_id', 'eventholder.id')
        ->join('event_venue','event.id','event_venue.event_id')
        ->join('venue','event_venue.venueList_id','venue.id')   
        ->join('user','event.bookingTakingById','user.id')     
        ->join('eventtype','event.eventTypeId','eventtype.id')   
        ->select('event.*','eventholder.name as eholderName','eventholder.phoneNumber as mobile','eventholder.email as email','venue.name as venue_name','user.name as fullname','eventtype.name as event_type')    
        ->where('event.id',$payment->event_id)
        ->first();
        if($events->eventTypeId == 1 or $events->eventTypeId ==2 or $events->eventTypeId == 3 ){
            $eventholder = 'Wedding Couple';
        } else
        {
            $eventholder = 'Event Holder';
        }
        $facilities = \DB::table('eventfacilities')
        ->join('facilities','eventfacilities.facility_id','facilities.id')
        ->select('facilities.*')
        ->where('eventfacilities.eventtype_id',$events->eventTypeId)
        ->get();
        $packages = \DB::connection('mysql2')->table('event_packagge')
        ->join('packagge','event_packagge.packaggeList_id','packagge.id')
        ->select('event_packagge.*','packagge.name')
        ->get();
       
       
        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $word_net = $f->format($payment->amount);
       
        $this->pdf->AddPage();
        // $this->pdf->SetTitle();
        $width_cell=array(15,35,65,18,55,15,30,22,23);
        
        
        // Title
        

        $this->pdf->SetFont('Arial','B',11);
        $this->pdf->Cell(190,7,'Confirmation Letter for '.$events->event_type,0,1,'C',false); // Third header column
        $this->pdf->SetFillColor(193,229,252); // Background color of header
        // $this->pdf->Cell(270,10,$dept,0,1,'R',false);
        $this->pdf->SetFont('Arial','',11);
        $this->pdf->SetLeftMargin(20);
            
            $this->pdf->Cell(145,7,'Date:',0,0,'R',false); // Third header column
            $this->pdf->Cell(20,7,date('d-m-y', strtotime($payment->payment_date)) ,0,1,'R',false); // Fourth header column
            // $this->pdf->Cell(45,7,'Event Type',0,0,'L',false); // Second header column
            // $this->pdf->Cell(45,7,': '.$events->event_type,0,1,'L',false); // Second header column
            $this->pdf->Cell(45,7,$eventholder,0,0,'L',false); // Second header column
            $this->pdf->Cell(45,7,': '.$events->name,0,1,'L',false); // Second header column
            $this->pdf->Cell(45,7,'Phone Number',0,0,'L',false); // Second header column
            $this->pdf->Cell(45,7,': '.$events->mobile,0,1,'L',false); // Second header column
            $this->pdf->Cell(45,7,'Email Address',0,0,'L',false); // Second header column
            $this->pdf->Cell(45,7,': '.$events->email,0,1,'L',false); // Second header column
            $this->pdf->Cell(45,7,'Booking Taken By',0,0,'L',false); // Second header column
            $this->pdf->Cell(45,7,': '.$events->fullname,0,1,'L',false); // Second header column
            //$this->pdf->Line(70,68,115,68); // Third header column
            $this->pdf->Ln(5);
            $this->pdf->Cell(45,7,'Dear Sir/Madam,',0,1,'L',false); // Second header column
            $this->pdf->Cell(145,7,'Thank you for selecting the Inya Lake Hotel as a venue for your '.$events->event_type.' on  ',0,1,'L',false); // Second header column
            $this->pdf->SetFont('Arial','BU',11);
            $this->pdf->Cell(18,7,date('d-m-y', strtotime($events->date)),0,0,'L',false); // Second header column
            $this->pdf->SetFont('Arial','',11);
            //$this->pdf->Line(162,88,182,88); // Third header column
            $this->pdf->Cell(150,7,'. We are very proud to hold this prestigious event in our hotel.',0,1,'L',false); // Second header column
            $this->pdf->SetFont('Arial','BU',11);
            $this->pdf->Cell(45,7,'Subject:',0,1,'L',false); // Second header column
            $this->pdf->SetFont('Arial','',11);
            // $this->pdf->SetLeftMargin(26);
            $this->pdf->Cell(7,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(45,7,'*  Event Date',0,0,'L',false); // Second header column
            $this->pdf->Cell(45,7,': '.date('dS-M-Y', strtotime($events->date)),0,1,'L',false); // Second header column
            $this->pdf->Cell(7,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(45,7,'*  Event Time',0,0,'L',false); // Second header column
            $this->pdf->Cell(45,7,': '.$events->time,0,1,'L',false); // Second header column
            $this->pdf->Cell(7,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(45,7,'*  Event Venue',0,0,'L',false); // Second header column
            $this->pdf->Cell(45,7,': '.$events->venue_name,0,1,'L',false); // Second header column
            $this->pdf->Cell(7,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(45,7,'*  Guaranteed Person',0,0,'L',false); // Second header column
            $this->pdf->Cell(45,7,': '.$events->numberOfPax,0,1,'L',false); // Second header column
            $this->pdf->Cell(7,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(45,7,'*  Expected Person',0,0,'L',false); // Second header column
            $this->pdf->Cell(45,7,': '.$events->expPax,0,1,'L',false); // Second header column
            $this->pdf->Cell(7,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(45,7,'*  Package Price',0,0,'L',false); // Second header column
            $this->pdf->Cell(3,7,':',0,0,'L',false); // Second header column
            
            foreach($packages as $package) {
                if($events->id==$package->event_id){
                    $this->pdf->Cell(45,7,$package->name,0,0,'L',false); // Second header column
                }
            }
                $this->pdf->Cell(45,7,'',0,1,'L',false); // Second header column
                
            $this->pdf->Ln(3);
            
            $this->pdf->Cell(150,7,'(Any variance between the expected and guaranteed number should be within 10%. Charges',0,1,'L',false); // Second header column
            $this->pdf->Cell(150,7,'will be made according to the guaranteed number or actual attendance, whichever is higher.)',0,1,'L',false); // Second header column

            $this->pdf->Ln(3);
            $this->pdf->SetFont('Arial','BU',11);
            $this->pdf->Cell(45,7,'Inclusive facilities:',0,1,'L',false); // Second header column
            $this->pdf->SetFont('Arial','B',11);
            foreach($facilities as $facility){
                $this->pdf->Cell(150,7,'- '.$facility->facility_name,0,1,'L',false); // Second header column
            }
            
            // $this->pdf->Cell(45,7,'- Complimentary flower arrangement & follow spot light',0,1,'L',false); // Second header column
            // $this->pdf->Cell(45,7,'- Complimentary Backdrop & five Tiered wedding cake',0,1,'L',false); // Second header column
            // $this->pdf->Cell(45,7,'- Complimentary one number of projector & screen',0,1,'L',false); // Second header column
            // $this->pdf->Cell(45,7,'- Complimentary use of a Dressing Room & one night Stay Room',0,1,'L',false); // Second header column
            // $this->pdf->Cell(45,7,'- Complimentary use of sound system',0,1,'L',false); // Second header column
            // $this->pdf->Cell(45,7,'- Complimentary Pre Wedding Photo shooting at ILH Garden',0,1,'L',false); // Second header column

            $this->pdf->Ln(3);            
            $this->pdf->SetFont('Arial','BU',11);
            $this->pdf->Cell(45,7,'1. Payment and Currency',0,1,'L',false); // Second header column
            $this->pdf->Ln(3);
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->SetFont('Arial','',11);
            $this->pdf->Cell(32,7,'- Deposit receive',0,0,'L',false); // Second header column
            $this->pdf->SetFont('Arial','BU',11);
            $this->pdf->Cell(30,7,$payment->currency.' '.$payment->amount,0,0,'L',false); // Second header column
            $this->pdf->SetFont('Arial','',11);
            $this->pdf->Cell(45,7,' at Inya Lake Hotel on ',0,0,'L',false); // Second header column
            $this->pdf->SetFont('Arial','BU',11);
            $this->pdf->Cell(30,7,date('dS-M-Y', strtotime($payment->payment_date)),0,1,'L',false); // Second header column
            $this->pdf->SetFont('Arial','',11);
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(32,7,'- Final payment must be settled 7 days ahead of function.',0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(32,7,'- The extra balance must be settled same day after the event.',0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(32,7,'- The organizer agrees that the settlement is in the currency as quoted above.',0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(32,7,'(If the organizer wants to settle in a different currency, the exchange rate will be set by the hotel.)   ',0,1,'L',false); // Second header column

            $this->pdf->SetFont('Arial','BU',11);
            $this->pdf->Cell(45,7,'2. Cancellation Policy',0,1,'L',false); // Second header column
            $this->pdf->Ln(3);
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->SetFont('Arial','',11);
            $this->pdf->Cell(32,7,'- All Deposits are non-refundable.',0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(45,7,'- The organizer agrees that if the event is cancelled 7days prior to the event date and  ',0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(30,7,'time mentioned above, 100% of total charges as per guaranteed person will be charged.',0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(32,7,'- 50% cancellation charge will apply if cancelled less than 14 days prior to the function.',0,1,'L',false); // Second header column
            $this->pdf->Ln(3);
            
            $this->pdf->SetFont('Arial','BU',11);
            $this->pdf->Cell(45,7,'3. Code of Behoviors',0,1,'L',false); // Second header column
            $this->pdf->SetFont('Arial','',11);
            $this->pdf->Cell(45,7,'The organizer(s) and his/her/their guests agree to follow the following rules set by the hotel.',0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(32,7,"- Illegal or immoral conduct in the hotel's premises during the event are strictly prohibited.",0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(32,7,'- Loud music or loud sound, or firecrackers that cause annoyance or disturbances to the ',0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(32,7,"  hotel's other guests or neighboring premises are not permitted under any circumstances.",0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(150,7,"- The Event must not exceed the event hours as mentioned above without the hotel's prior",0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Cell(32,7,"  approval.",0,1,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Multicell(160,7,"- All the activities of the event conducted by the organizer(s) and his/her/their guests on the hotel's premises during the event hours must be in conformity with the laws of Myanmar.",0,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Multicell(160,7,"- All damages or injury caused at the hotel's premises or common areas, or damage of the hotel's property and equipment caused by the organizer(s) or his/her/their sub-contractor or guests must be promptly repaired by the organizer or if not, organizer will be charged at the value advised by a supplier or a contractor appointed by the hotel.",0,'L',false); // Second header column
            $this->pdf->Cell(10,7,'',0,0,'L',false); // Tab Space
            $this->pdf->Multicell(160,7,"- Any complaint with regard to the event must be brought on the same day right after the event. Otherwise, the complaints shall not be considered by the hotel.",0,'L',false); // Second header column
            $this->pdf->Ln(3);
            $this->pdf->Multicell(170,7,"Thank you for selecting our hotel and we assure you that we will provide our best service to your valued Event.",0,'L',false); // Second header column
            $this->pdf->Ln(6);
            $this->pdf->SetFont('Arial','B',11);
            $this->pdf->Cell(85,7,"Prepared by",0,0,'L',false); // Second header column
            $this->pdf->Cell(85,7,"Agreed and accepted by",0,1,'R',false); // Second header column
            $this->pdf->Ln(4);
            $this->pdf->SetFont('Arial','',11);
            $this->pdf->Cell(85,7,"Event Sales (Inya Lake Hotel)",0,0,'L',false); // Second header column
            $this->pdf->Cell(85,7,"Wedding Couple",0,1,'R',false); // Second header column

            $this->pdf->Ln(20);
            $this->pdf->Cell(85,7,"_____________________",0,0,'L',false); // Second header column
            $this->pdf->Cell(85,7,"_____________________",0,1,'R',false); // Second header column




        $this->pdf->Output();
        exit;
    }

}
