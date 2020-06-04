<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses=Auth::user()->addresses()->get();
        return view('layouts.user.addresses.index',compact('addresses'));
    }
}
