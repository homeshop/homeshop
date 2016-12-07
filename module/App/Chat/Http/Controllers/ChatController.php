<?php namespace App\Chat\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class ChatController extends Controller {
	
	public function index()
	{
		return view('chat::index');
	}
	
}