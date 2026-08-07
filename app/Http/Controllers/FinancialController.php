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
        $goals = $financials->where('type', 'goal');
        $tithings = $financials->where('type', 'tithing');
        $expenses = $financials->where('type', 'expense');
        $savings = $financials->where('type', 'saving');
        
        $totalSavings = $savings->where('is_completed', true)->sum('amount');
        $totalExpenses = $expenses->where('is_completed', true)->sum('amount');
        $totalTithing = $tithings->where('is_completed', true)->sum('amount');
        
        return view('financials.index', compact(
            'financials', 'goals', 'tithings', 'expenses', 'savings',
            'totalSavings', 'totalExpenses', 'totalTithing'
        ));
    }
    
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:goal,tithing,expense,saving',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'nullable|in:weekly,monthly,one-time',
            'due_date' => 'nullable|date',
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
