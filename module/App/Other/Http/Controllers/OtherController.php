<?php namespace App\Other\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class OtherController extends Controller {
	
	public function index()
	{
		return view('other::index');
	}
	
}