<?php namespace App\Topic\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class TopicController extends Controller {
	
	public function index()
	{
		return view('topic::index');
	}
	
}