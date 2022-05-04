<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    public function index(Request $request) {

        $user = auth()->user();

        return view('livestream')->with([
            'user' => collect($user->only(['id', 'name'])),
        ]);
    }
}
