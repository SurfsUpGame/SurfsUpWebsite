<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonationController extends Controller
{
    public function start(Request $request, User $user)
    {
        // Check if current user has admin role
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // Don't allow impersonating yourself
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot impersonate yourself.');
        }

        // Store the original user ID in session
        Session::put('impersonator_id', auth()->id());

        // Login as the target user
        Auth::login($user);

        return redirect('/')->with('success', "You are now impersonating {$user->name}");
    }

    public function stop()
    {
        // Check if we're currently impersonating
        if (!Session::has('impersonator_id')) {
            return redirect('/')->with('error', 'You are not currently impersonating anyone.');
        }

        // Get the original user
        $originalUser = User::find(Session::get('impersonator_id'));

        if (!$originalUser) {
            Session::forget('impersonator_id');
            return redirect('/')->with('error', 'Original user not found. Please login again.');
        }

        // Remove impersonation session
        Session::forget('impersonator_id');

        // Login back as original user
        Auth::login($originalUser);

        return redirect('/')->with('success', 'Stopped impersonating. You are now logged in as yourself.');
    }

    public function userList()
    {
        // Check if current user has admin role
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $users = User::orderBy('name')->paginate(20);

        return view('admin.impersonation', compact('users'));
    }
}
