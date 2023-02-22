<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function create()
  {
    return view('users.register');
  }

  public function store(Request $request)
  {
    $formFields = $request->validate([
      'name' => 'required|min:3',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|confirmed|min:6'
    ]);

    $formFields['password'] = bcrypt($formFields['password']);

    $user = User::create($formFields);

    auth()->login($user);

    return redirect('/')->with('message', 'Logged in and user created successfully');
  }

  public function destroy(Request $request)
  {
    auth()->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/')->with('message', 'Logged out successfully');
  }

  public function login()
  {
    return view('users.login');
  }

  public function authenticate(Request $request)
  {
    $formFields = $request->validate([
      'email' => 'required|email',
      'password' => 'required'
    ]);

    if (auth()->attempt($formFields)) {
      $request->session()->regenerate();

      return redirect('/')->with('message', 'You\'re now logged in!');
    } else {
      return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }
  }
}
