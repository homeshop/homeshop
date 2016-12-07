<?php namespace App\Mobile\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class MobileController extends Controller {
	
	public function index()
	{
		return view('mobile::index');
	}
	
}