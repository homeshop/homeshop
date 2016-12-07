<?php namespace App\Member\Http\Controllers;

use Homeshop\Modules\Routing\Controller;

class MemberController extends Controller {
    
    public function index() {
        return view('member::index');
    }
}