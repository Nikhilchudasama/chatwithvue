<?php

namespace App\Http\Controllers;

use App\User;
use App\Message;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * User Login
     *
     * @return json
     **/
    public function login()
    {
        $validatedData = request()->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt($validatedData)) {
            $users = User::all();
            $messages = Message::with('user:id,name')->get();

            return [
                'type' => 'success',
                'users' => $users->toJson(),
                'messages' => $messages->toJson(),
                'user_id' => Auth::user()->id,
            ];
        }

        return [
            'type' => 'error',
            'message' => 'Creadential not match.',
        ];
    }

    /**
     * Check user logged in.
     *
     * @return Json
     **/
    public function checkUserLoggedIn()
    {
        if (Auth::check()) {
            $users = User::all();
            $messages = Message::with('user:id,name')->get();

            return [
                'type' => 'success',
                'users' => $users->toJson(),
                'messages' => $messages->toJson(),
                'user_id' => Auth::user()->id,
            ];
        }
        return [
            'type' => 'error',
            'message' => 'User not logged in.',
        ];
    }

    /**
     * Store user message
     *
     * @return Json
     **/
    public function sendMessage()
    {
        $validatedData = request()->validate([
            'text' => 'required|string|max:255',
        ]);

        $message = Auth::user()->messages()->create($validatedData);

        $options = array(
            'cluster' => 'ap2',
            'encrypted' => true
        );
        $pusher = new \Pusher\Pusher(
            '5d4116bdd244e80ce28f',
            'a22ec4ee9ef53aca7c8e',
            '576362',
            $options
        );

        $data = [
            'id' => $message->id,
            'text' => $message->text,
            'userId' => $message->user_id,
            'userName' => $message->user->name,
            'userImage' => '/images/' . $message->user_id . '.png',
            'time' => $message->created_at->format('g:i A'),
            'date' => $message->created_at->format('F d'),
        ];

        $pusher->trigger('my-channel', 'my-event', $data);

        return $message->load('user:id,name');
    }
}
