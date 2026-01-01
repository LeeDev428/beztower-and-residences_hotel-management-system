<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('customer.home');
    }

    public function about()
    {
        return view('customer.about');
    }

    public function services()
    {
        return view('customer.services');
    }

    public function contact()
    {
        return view('customer.contact');
    }
}

