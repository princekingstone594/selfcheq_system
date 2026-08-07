<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-emerald-900/40 via-slate-900 to-teal-900/30 p-6 shadow-2xl shadow-emerald-950/30 backdrop-blur-xl sm:p-8">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-emerald-500/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-teal-500/20 blur-3xl"></div>
            </div>

            <div class="relative">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-300">Financials</p>
                <h1 class="mt-2 text-3xl font-bold text-white sm:text-4xl">Manage Your Finances</h1>
                <p class="mt-2 text-slate-300">Track goals, tithing, expenses, and savings.</p>
            </div>
        </section>

        <!-- Stats Overview -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Total Savings</p>
                <p class="mt-2 text-2xl font-bold text-white">${{ number_format($totalSavings ?? 0, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Total Expenses</p>
                <p class="mt-2 text-2xl font-bold text-white">${{ number_format($totalExpenses ?? 0, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-amber-400/20 bg-amber-500/10 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Total Tithing</p>
                <p class="mt-2 text-2xl font-bold text-white">${{ number_format($totalTithing ?? 0, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-400/20 bg-indigo-500/10 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Net Worth</p>
                <p class="mt-2 text-2xl font-bold text-white">${{ number_format(($totalSavings ?? 0) - ($totalExpenses ?? 0), 2) }}</p>
            </div>
        </section>

        <!-- Add New Financial -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Add New Entry</p>
            
            <form method="POST" action="{{ route('financials.store') }}" class="mt-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Type</label>
                        <select name="type" required class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="goal">Goal</option>
                            <option value="tithing">Tithing</option>
                            <option value="expense">Expense</option>
                            <option value="saving">Saving</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Title</label>
                        <input type="text" name="title" required class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Amount ($)</label>
                        <input type="number" step="0.01" name="amount" required class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Frequency</label>
                        <select name="frequency" class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="one-time">One-time</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Due Date</label>
                        <input type="date" name="due_date" class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-300">Description</label>
                    <textarea name="description" rows="2" class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>
                
                <button type="submit" class="mt-4 rounded-2xl bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-400 transition">
                    Add Entry
                </button>
            </form>
        </section>

        <!-- Financial Entries -->
        <section class="space-y-4">
            @if($financials->count() > 0)
                @foreach($financials as $financial)
                    <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg {{ $financial->is_completed ? 'opacity-60' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    @if($financial->type === 'goal')
                                        <span class="text-emerald-400">🎯</span>
                                    @elseif($financial->type === 'tithing')
                                        <span class="text-amber-400">🙏</span>
                                    @elseif($financial->type === 'expense')
                                        <span class="text-rose-400">💸</span>
                                    @elseif($financial->type === 'saving')
                                        <span class="text-sky-400">💰</span>
                                    @endif
                                    <p class="font-semibold text-white">{{ $financial->title }}</p>
                                </div>
                                
                                @if($financial->description)
                                    <p class="mt-1 text-sm text-slate-400">{{ $financial->description }}</p>
                                @endif
                                
                                <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-400">
                                    <span class="rounded-full bg-slate-800 px-3 py-1">${{ number_format($financial->amount, 2) }}</span>
                                    @if($financial->frequency)
                                        <span class="rounded-full bg-slate-800 px-3 py-1">{{ ucfirst($financial->frequency) }}</span>
                                    @endif
                                    @if($financial->due_date)
                                        <span class="rounded-full bg-slate-800 px-3 py-1">Due: {{ \Carbon\Carbon::parse($financial->due_date)->format('M d, Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('financials.toggle', $financial) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded-xl border border-white/10 bg-slate-800 p-2 text-slate-300 hover:text-white transition">
                                        {{ $financial->is_completed ? '↩️' : '✅' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('financials.destroy', $financial) }}" class="inline" onsubmit="return confirm('Delete this entry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl border border-rose-500/20 bg-slate-800 p-2 text-rose-400 hover:text-rose-300 transition">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="rounded-2xl border border-dashed border-white/10 p-8 text-center">
                    <p class="text-slate-400">No financial entries yet. Start tracking your finances above.</p>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>