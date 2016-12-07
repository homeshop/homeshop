<?php namespace App\Api\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class ApiController extends Controller {
	
	public function index()
	{
		return view('api::index');
	}
	
}