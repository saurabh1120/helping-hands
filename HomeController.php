<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Food;
use Illuminate\Support\Facades\DB;
use App\DonatedItem;
use App\Feedback;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function views()
    {
        $foods = DonatedItem::where('user_id', auth()->user()->id)->get();
        return view('layouts.view')->with('foods', $foods);
    }

    public function accept_donation($id)
    {
        $food = DonatedItem::find($id);
        $food->isAvailable = 0;
        $food->isAccepted = 1;
        $food->save();
        return redirect('/accepted_donations_view')->with('success', 'Donation Accepted Successfully');

    }

    public function user_delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect('/home')->with('success', 'User Deleted Successfully');

    }

    public function complete_donation($id)
    {
        $food = DonatedItem::find($id);
        $food->isCompleted = 1;
        $food->isAccepted = 0;
        $food->save();
        return redirect('/completed_donations_view')->with('success', 'Donation Completed Successfully');

    }

    public function add_feedback()
    {
        return view('admin.ngo_feedback');

    }

    public function save_feedback(Request $request)
    {
        $feedback = [
            'user_id' => $request->user()->id,
            'feedback_message' => $request['feedback_message'],
            'created_at' => date('Y-m-d')

        ];
        DB::table('feedback')->insert($feedback);
        return redirect('/home')->with('success', 'Feedback Sent Successfully');

    }

    public function ngo_feedbacks()
    {
        $uids = User::where('isNGO',1)->pluck('id');
        $feedbacks = Feedback::whereIn('user_id',$uids)->get();
        return view('admin.ngo_feedbacks')->with('feedbacks', $feedbacks);
    }
    public function user_feedbacks()
    {
        $uids = User::where('isNGO',0)->pluck('id');
        $feedbacks = Feedback::whereIn('user_id',$uids)->get();
        return view('admin.user_feedbacks')->with('feedbacks', $feedbacks);
    }


    public function admin_view()
    {
        $foods = DonatedItem::all();
        return view('admin.view')->with('foods', $foods);
    }
    public function available_donations_view()
    {
        $foods = DonatedItem::where('isAvailable',1)->get();
        return view('admin.available_donations_view')->with('foods', $foods);
    }

    public function accepted_donations_view()
    {
        $foods = DonatedItem::where('isAccepted',1)->get();
        return view('admin.accepted_donations_view')->with('foods', $foods);
    }

    public function completed_donations_view()
    {
        $foods = DonatedItem::where('isCompleted',1)->get();
        return view('admin.completed_donations_view')->with('foods', $foods);
    }

    public function all_users()
    {
        $users = User::where('isAdmin',0)->where('isNGO',0)->get();
        return view('admin.allusers')->with('users', $users);
    }

    public function all_reg_users()
    {
        $users = User::where('isAdmin',0)->where('isNGO',0)->get();
        return view('admin.all-reg-users')->with('users', $users);
    }
}
