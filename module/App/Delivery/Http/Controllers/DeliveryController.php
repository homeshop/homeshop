<?php namespace App\Delivery\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class DeliveryController extends Controller {
	
	public function index()
	{
		return view('delivery::index');
	}
	
}