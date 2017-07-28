<?php

namespace JimHlad\LeapFrog\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CrudController extends Controller
{

    /**
     * Display the CRUD generation form
     * 
     * @return Response
     */
    public function index()
    {
    	return view('leapfrog::crud');
    }

    /**
     * Handle CRUD form submission
     * 
     * @param  Request $request
     * @return Response
     */
    public function generate(Request $request)
    {
    	return($request->all());
    }
}