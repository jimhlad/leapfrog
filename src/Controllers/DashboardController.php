<?php

namespace JimHlad\LeapFrog\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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