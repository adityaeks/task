<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiffCheckerController extends Controller
{
    /**
     * Display the diff checker tool page.
     */
    public function index()
    {
        return view('diff-checker.index');
    }
}
