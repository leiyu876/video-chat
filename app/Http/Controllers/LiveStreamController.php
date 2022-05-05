<?php

namespace App\Http\Controllers;

use App\Events\GroupCreated;
use App\Models\Group;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    public function index(Request $request) {

        $user = auth()->user();

        /** Create Group START */
        // $group = Group::create(['name' => 'leo-live-streaming']);
        // $users = collect(request('users'));
        // $users->push(auth()->user()->id);
    
        // $group->users()->attach($users);

        // broadcast(new GroupCreated($group))->toOthers();
        /** Create Group END */

        return view('livestream')->with([
            'user' => collect($user->only(['id', 'name'])),
            'group' => Group::first()
        ]);
    }
}
