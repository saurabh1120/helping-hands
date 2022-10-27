<?php

namespace App\Http\Controllers;

use App\DonatedItem;
use Illuminate\Http\Request;

class DonatedItemController extends Controller
{
    //
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
            $path = $request->file('filename')->storeAs('public/images', $fileNameToStore);
        } else {
            $fileNameToStore = 'noimage.jpg';
        }
        // Create Post
        $donated_item = new DonatedItem();
        $donated_item->user_id = auth()->user()->id;
        $donated_item->item_name = $request->input('name');
        $donated_item->category = $request->input('category');
        $donated_item->address = $request->input('address');
        $donated_item->item_description = $request->input('description');
        $donated_item->item_image = $fileNameToStore;
        $donated_item->landmark = $request->input('landmark');
        $donated_item->mobile = $request->input('mobile');
        $donated_item->save();
        return redirect('/home')->with('success', 'Donation Added Successfully');
    }
}
