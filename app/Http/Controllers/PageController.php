<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function dashboard()
    {
        return view('pages.dashboard');
    }

    public function calendar()
    {
        return view('pages.calendar');
    }

    public function cars()
    {
        return view('pages.cars');
    }

    public function carDetails($kennzeichen)
    {
        return view('pages.cardetails', ['kennzeichen' => $kennzeichen]);
    }

    public function customers()
    {
        return view('pages.customers');
    }

    public function customerDetails($id)
    {
        $customer = \App\Models\Customer::with('auftraege')->findOrFail($id);
        return view('pages.customerdetails', ['id' => $id, 'auftraege' => $customer->auftraege]);
    }
    
    public function jobs()
    {
        return view('pages.jobs');
    }

    public function jobdetails($id)
    {
        return view('pages.jobdetails', ['id' => $id]);
    }
 
    public function reports()
    {
        return view('pages.reports');
    }

    public function chat()
    {
        return view('pages.chat');
    }

    public function profile()
    {
        return view('pages.profile');
    }

    public function settings()
    {
        return view('pages.settings');
    }

    public function login()
    {
        return view("auth.login");
    }

    public function loginPost()
    {
        return redirect()->route('dashboard');
    }

    public function users()
    {
        return view('pages.users');
    }

}