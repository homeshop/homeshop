<?php namespace App\Cms\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class CmsController extends Controller {
	
	public function index()
	{
		return view('cms::index');
	}
	
}