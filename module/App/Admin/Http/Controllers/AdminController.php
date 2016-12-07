<?php namespace App\Admin\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class AdminController extends Controller {
	
	public function index()
	{
		return view('admin::index');
	}
	
}