<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function index()
    {
        return view('admin.dashboard', [
            'admin' => Auth::user(),
            'stats' => [
                'questions'  => 0,
                'scraped'    => 0,
                'ai_drafts'  => 0,
                'published'  => 0,
            ]
        ]);
    }
}
