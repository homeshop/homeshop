<?php namespace App\Core\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class CoreController extends Controller {
	
	public function index()
	{
		return view('core::index');
	}
	
}