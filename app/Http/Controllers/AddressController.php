<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses=Auth::user()->addresses()->get();
        return view('layouts.user.addresses.index',compact('addresses'));
    }

    public function create()
    {
        return view('layouts.user.addresses.add_and_edit');
    }
    public function edit(UserAddress $address)
    {
        $this->authorize('own',$address);
        return view('layouts.user.addresses.add_and_edit',compact('address'));
    }

    public function store(UserAddressRequest $request)
    {
        $request->user()->addresses()->create($request->only([
            'zip','address','contact_name','contact_phone',
            'province','city','district'
        ]));
        return redirect()->route('user.addresses.index');
    }

    public function destroy(UserAddress $address)
    {
        $this->authorize('own',$address);
        $address->delete();
        return [];
    }

    public function update(UserAddress $address,UserAddressRequest $request)
    {
        $this->authorize('own',$address);
        $address->update($request->only([
            'address','zip','contact_name','contact_phone',
            'province','city','district'
        ]));
        return redirect()->route('user.addresses.index');
    }
}
