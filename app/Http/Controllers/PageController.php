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

    public function carDetails()
    {
        return view('pages.cardetails');
    }

    public function customers()
    {
        return view('pages.customers');
    }

    public function customerDetails()
    {
        return view('pages.customerdetails');
    }
    
    public function jobs()
    {
        return view('pages.jobs');
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

}
