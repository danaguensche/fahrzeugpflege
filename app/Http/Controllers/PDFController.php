<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class PDFController extends Controller
{
    //
    public function choosePDF(string $pdfType) {
        switch ($pdfType) {
            case "kundenauftrag":
                return generatePDFKundenauftrag();
            case "uebergabeprotokoll":
                return generatePDFUebergabeprotokoll();
        }
        // no match
        return view('pages.reports');
    }
    private function generatePDFKundenauftrag() {
        // Portrait, 1 Page
        // return pdf
    }
    private function generatePDFUebergabeprotokoll() {
        // Landscape, 4 Pages
        // return pdf
    }
}
