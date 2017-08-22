<?php

namespace JimHlad\LeapFrog\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{

    /**
     * Display the main dashboard
     * 
     * @return Response
     */
    public function index()
    {
    	return view('leapfrog::dashboard');
    }
}