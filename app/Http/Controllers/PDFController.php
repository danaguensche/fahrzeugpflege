<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class PDFController extends Controller
{
    //
    
    public function generatePDFKundenauftrag() {
        // Portrait, 1 Page
    }
    public function generatePDFUebergabeprotokoll() {
        // Landscape, 4 Pages
    }
}
