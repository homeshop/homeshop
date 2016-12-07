<?php namespace App\Docs\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class DocsController extends Controller {
	
	public function index()
	{
		return view('docs::index');
	}
	
}