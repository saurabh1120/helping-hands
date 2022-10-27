<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\DonatedItem;

class HelpingHandsController extends Controller
{


    public function save_item(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'item_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $item_image = '';
        try {
            if ($request->hasFile('half_body_image')) {
                $item_image = time() . "_" . $request->file('half_body_image')->getClientOriginalName();
                $request->file('half_body_image')->move(public_path('images'), $item_image);
            }
            $donated_item = [
                'user_id' => $request->user()->id,
                'item_name' => $request['item_name'],
                'item_description' => $request['item_description'],
                'item_image' => $item_image,
                'category' => $request['category'],
                'address' => $request['address'],
                'mobile' => $request['mobile'],
                'landmark' => $request['landmark'],
                'isAvailable' => True,
                'isAccepted' => False,
                'isCompleted' => False
            ];
            DB::table('donated_items')->insert($donated_item);
            return response()->json([
                'message'  => 'Item added successfully!',
                //'user' => $request->user()->id,
            ], 200);

            /*Mail::to($email)->send(new \App\Mail\OrderPlaced($o_id));*/
        } catch (\Exception $e) {
            return response()->json([$e->getMessage(), 'Error'], 403);
        }
    }

    public function save_feedback(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'feedback_message' => 'required',
            'created_at' => date('Y-m-d')
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        try {

            $feedback = [
                'user_id' => $request->user()->id,
                'feedback_message' => $request['feedback_message'],

            ];
            DB::table('feedback')->insert($feedback);
            return response()->json([
                'message'  => 'Feedback added successfully!',
                //'user' => $request->user()->id,
            ], 200);

            /*Mail::to($email)->send(new \App\Mail\OrderPlaced($o_id));*/
        } catch (\Exception $e) {
            return response()->json([$e->getMessage(), 'Error'], 403);
        }
    }

    public function donated_items(Request $request)
    {
        $user = $request->user();
        $donated_items =  DB::table('donated_items')->where('user_id', $user->id)->get();
        return response()->json([
            'message'  => 'Item retrieved successfully!',
            'donated_items' => $donated_items,
        ], 200);
    }

    public function all_donated_items(Request $request)
    {
        $user = $request->user();
        $donated_items =  DB::table('donated_items')->join('users', 'donated_items.user_id', '=', 'users.id')->get();
        return response()->json([
            'message'  => 'Item retrieved successfully!',
            'donated_items' => $donated_items,
        ], 200);
    }

    public function accepted_items(Request $request)
    {
        $user = $request->user();
        $donated_items =  DB::table('donated_items')->where('user_id', $user->id)->where('isAccepted',1)->get();
        return response()->json([
            'message'  => 'Item retrieved successfully!',
            'donated_items' => $donated_items,
        ], 200);
    }

    public function completed_items(Request $request)
    {
        $user = $request->user();
        $donated_items =  DB::table('donated_items')->where('user_id', $user->id)->where('isCompleted',1)->get();
        return response()->json([
            'message'  => 'Item retrieved successfully!',
            'donated_items' => $donated_items,
        ], 200);
    }


}
