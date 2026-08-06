<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark',
        ]);

        Auth::user()->update([
            'theme' => $request->theme,
        ]);

        return back()->with('status', 'theme-updated');
    }

    public function updatePermissions(Request $request)
    {
        $request->validate([
            'notifications_enabled' => 'nullable|boolean',
            'contacts_enabled' => 'nullable|boolean',
            'reminders_enabled' => 'nullable|boolean',
        ]);

        Auth::user()->update([
            'notifications_enabled' => $request->has('notifications_enabled'),
            'contacts_enabled' => $request->has('contacts_enabled'),
            'reminders_enabled' => $request->has('reminders_enabled'),
        ]);

        return back()->with('status', 'permissions-updated');
    }
}