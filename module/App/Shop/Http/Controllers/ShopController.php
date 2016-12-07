<?php namespace App\Shop\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class ShopController extends Controller {
	
	public function index()
	{
		return view('shop::index');
	}
	
}