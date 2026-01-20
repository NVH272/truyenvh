<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function about()
    {
        // Trả về view: resources/views/user/policy/about.blade.php
        return view('user.policy.about');
    }

    public function terms()
    {
        // Trả về view: resources/views/user/policy/term.blade.php
        return view('user.policy.terms');
    }

    public function privacy()
    {
        // Trả về view: resources/views/user/policy/privacy.blade.php
        return view('user.policy.privacy');
    }

    public function contact()
    {
        // Trả về view: resources/views/user/policy/contact.blade.php
        return view('user.policy.contact');
    }

    public function disclaimer()
    {
        // Trả về view: resources/views/user/policy/disclaimer.blade.php
        return view('user.policy.disclaimer');
    }
}
