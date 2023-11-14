<?php

namespace App\Models;

use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pdf extends Fpdf
{
    use HasFactory;
    public function Header()
    {
        
        // Logo
        $this->Image('logo.png',85,6,40);
        // Arial bold 15
        $this->SetFont('Arial','B',11);
        // Move to the right
        $this->Cell(80);
        // Title
       
        // Line break
        $this->Ln(20);
    }

    // Page footer
    public function Footer()
    {

        // Position at 1.5 cm from bottom
        $this->SetY(-16);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,6, 'Kaba Aye Pagoda Road, Yangon, Myanmar',0,1,'C');
        $this->Cell(0,6, 'Tel: 01-9662866, 9662857, Fax: 9665537, Email: inyalake@inyalakehotel.com, Website: www.inyalakehotel.com',0,0,'C');

    }
}
