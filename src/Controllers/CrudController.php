<?php

namespace JimHlad\LeapFrog\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JimHlad\LeapFrog\Services\CrudService;

class CrudController extends Controller
{

    /**
     * CrudService
     *
     * @var CrudService
     */
    protected $crudService;

    /**
     * Construct our controller
     * 
     * @param CrudService $crudService
     */
    public function __construct(CrudService $crudService)
    {
        $this->crudService = $crudService;
    }

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
        return $this->crudService->generate($request->all());
    }
}