<?php namespace App\Install\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class InstallController extends Controller {
	
	public function index()
	{
		return view('install::index');
	}
	
}