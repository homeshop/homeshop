<?php namespace App\Circle\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class CircleController extends Controller {
	
	public function index()
	{
		return view('circle::index');
	}
	
}