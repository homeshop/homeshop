<?php namespace App\Chain\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class ChainController extends Controller {
    public function index() {
        return view('chain::index');
    }
}