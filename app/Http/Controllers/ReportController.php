<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
    public function chooseForm(string $formType) {
        switch ($formType) {
            case "kundenauftrag":
                return $this->formKundenauftrag();
            case "uebergabeprotokoll":
                return $this->formUebergabeProtokoll();
        }
        // no match
        return view('pages.reports');
    }
    private function formKundenauftrag() {
        //return view('forms.kundenauftrag');
    }
    private function formUebergabeprotokoll() {
        //return view('forms.uebergabeprotokoll');
    }
}
