<?php namespace App\Microshop\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class MicroshopController extends Controller {
    
    public function index() {
        return view('microshop::index');
    }
}