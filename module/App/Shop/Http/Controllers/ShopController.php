<?php namespace App\Shop\Http\Controllers;


class ShopController extends Controller {
	
	public function index()
	{
		return view('shop::index');
	}
	
}