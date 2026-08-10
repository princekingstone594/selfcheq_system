<?php

namespace App\Http\Controllers;

use App\Models\Financial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FinancialController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        
        $financials = $user->financials()->latest()->get();
        
        // Separate by type
        $bills = $financials->where('type', 'bill');
        $tithers = $financials->where('type', 'tithe');
        $savings = $financials->where('type', 'saving');
        
        // Paid vs unpaid counts
        $paidBills = $bills->where('is_completed', true);
        $unpaidBills = $bills->where('is_completed', false);
        
        return view('financials.index', compact(
            'financials',
            'bills',
            'tithers',
            'savings',
            'paidBills',
            'unpaidBills'
        ));
    }
    
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:saving,tithe,bill',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'frequency' => 'nullable|in:weekly,monthly,one-time,quarterly,annually',
            'due_date' => 'nullable|date',
            'reminder_days' => 'nullable|integer|min:0',
        ]);
        
        $request->user()->financials()->create($validated);
        
        return redirect()->route('financials.index')->with('status', 'financial-added');
    }
    
    public function toggle(Financial $financial): RedirectResponse
    {
        $this->authorize('update', $financial);
        $financial->update(['is_completed' => !$financial->is_completed]);
        
        return back()->with('status', 'financial-updated');
    }
    
    public function destroy(Financial $financial): RedirectResponse
    {
        $this->authorize('delete', $financial);
        $financial->delete();
        
        return back()->with('status', 'financial-deleted');
    }
}