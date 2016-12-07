<?php namespace App\Wap\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class WapController extends Controller {
	
	public function index()
	{
		return view('wap::index');
	}
	
}