<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\NGO;
use App\User;


class NGOController extends Controller
{
    //
    public function admin_view()
    {
        $ngos = NGO::all();
        return view('admin.view_ngos')->with('ngos', $ngos);
    }
    public function add_ngo()
    {
        return view('admin.add_ngo');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $ngo = NGO::find($id);
        $ngo->delete();

        return redirect('/all-ngos')->with('success', 'NGO Deleted Successfully');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Create Post
        $user = User::create([
            'name' => $request->input('ngo_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('contact_number'),
            'password' => Hash::make($request->input('contact_number')),
        ]);
        $user->isNGO = 1;
        $user->save();
        $ngo = new NGO;
        $ngo->user_id = $user->id;
        $ngo->address = $request->input('address');
        $ngo->city = $request->input('city');
        $ngo->pincode = $request->input('pincode');
        $ngo->contact_person = $request->input('contact_person');
        $ngo->contact_number = $request->input('contact_number');
        $ngo->save();
        return redirect('/all-ngos')->with('success', 'NGO Added Successfully');
    }
}
