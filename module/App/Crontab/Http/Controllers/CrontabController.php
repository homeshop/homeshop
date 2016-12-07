<?php namespace App\Crontab\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class CrontabController extends Controller {
	
	public function index()
	{
		return view('crontab::index');
	}
	
}