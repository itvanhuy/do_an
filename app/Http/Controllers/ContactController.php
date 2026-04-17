<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required'
        ]);

        // Simulating message storage (or you can use a real DB table)
        // For now, redirect with success.
        
        return back()->with('success', 'Your message has been sent successfully. We will get back to you soon!');
    }
}
