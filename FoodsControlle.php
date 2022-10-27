<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Food;
use DB;
use App\DonatedItem;

use Illuminate\Http\Request;

class FoodsControlle extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('layouts.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->hasFile('filename')) {
            // Get filename with the extension
            $filenameWithExt = $request->file('filename')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('filename')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            // Upload Image
            $path = $request->file('filename')->storeAs('public/filenames', $fileNameToStore);
        } else {
            $fileNameToStore = 'noimage.jpg';
        }
        // Create Post
        $food = new Food;
        $food->userid = auth()->user()->id;
        $food->name = $request->input('name');
        $food->foodname = $request->input('foodname');
        $food->address = $request->input('address');
        $food->description = $request->input('description');
        $food->filename = $fileNameToStore;
        $food->save();
        return redirect('/home')->with('success', 'Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $food = DonatedItem::find($id);
       $location = $food->address.",".$food->landmark.",Karnataka,India";
        $url="https://api.mapbox.com/geocoding/v5/mapbox.places/$location.json?access_token=pk.eyJ1Ijoic2hla2hhcjNrIiwiYSI6ImNrZWJ6YTYxYzA0Y2cyeWx0NWJhN2g0OXQifQ.FVxtFpIamNfgPoXJVWCrCg";
        $method = "GET";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url, //updateVideoData
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        $coordinates= $response->features[0]->geometry->coordinates;
        return view('admin.show',compact('food','coordinates'));
    }

    public function map($id)
    {
        $food = DonatedItem::find($id);
       $location = $food->address.",".$food->landmark.",Karnataka,India";
        $url="https://api.mapbox.com/geocoding/v5/mapbox.places/$location.json?access_token=pk.eyJ1Ijoic2hla2hhcjNrIiwiYSI6ImNrZWJ6YTYxYzA0Y2cyeWx0NWJhN2g0OXQifQ.FVxtFpIamNfgPoXJVWCrCg";
        $method = "GET";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url, //updateVideoData
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        $coordinates= $response->features[0]->geometry->coordinates;
        return view('admin.map',compact('food','coordinates'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
