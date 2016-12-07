<?php namespace App\Club\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class ClubController extends Controller {
	
	public function index()
	{
		return view('club::index');
	}
	
}