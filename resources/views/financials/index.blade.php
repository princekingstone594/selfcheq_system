<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <section class="relative overflow-hidden rounded-3xl border border-emerald-400/20 bg-gradient-to-br from-emerald-900/30 via-slate-900 to-teal-900/30 p-6 shadow-2xl shadow-emerald-950/30 backdrop-blur-xl sm:p-8">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-emerald-500/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-teal-500/20 blur-3xl"></div>
            </div>

            <div class="relative">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-300">Financial Planning</p>
                <h1 class="mt-2 text-3xl font-bold text-white sm:text-4xl">Your Financial Journey</h1>
                <p class="mt-2 text-slate-300">Plan, track, and achieve your financial goals with purpose.</p>
                
                <!-- Quote -->
                <div class="mt-4 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 backdrop-blur-sm">
                    <p class="text-sm italic text-emerald-100">"The art is not in making money, but in keeping it. Plan wisely, spend mindfully, and watch your wealth grow."</p>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="grid gap-4 sm:grid-cols-3">
            <a href="{{ route('financials.index') }}?filter=savings" class="group rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-5 shadow-lg transition hover:border-emerald-400/40 hover:bg-emerald-500/15">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">💰</span>
                    <div>
                        <p class="text-sm font-semibold text-emerald-300">Saving Plans</p>
                        <p class="text-xs text-slate-400 mt-0.5">Set & track goals</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('financials.index') }}?filter=tithe" class="group rounded-2xl border border-amber-400/20 bg-amber-500/10 p-5 shadow-lg transition hover:border-amber-400/40 hover:bg-amber-500/15">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🙏</span>
                    <div>
                        <p class="text-sm font-semibold text-amber-300">Tithe Reminders</p>
                        <p class="text-xs text-slate-400 mt-0.5">Honor your giving</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('financials.index') }}?filter=bills" class="group rounded-2xl border border-rose-400/20 bg-rose-500/10 p-5 shadow-lg transition hover:border-rose-400/40 hover:bg-rose-500/15">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">📋</span>
                    <div>
                        <p class="text-sm font-semibold text-rose-300">Bills & Payments</p>
                        <p class="text-xs text-slate-400 mt-0.5">Never miss a due date</p>
                    </div>
                </div>
            </a>
        </section>

        <!-- Add New Financial Plan -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Create Financial Plan</p>
            
            <form method="POST" action="{{ route('financials.store') }}" class="mt-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Type</label>
                        <select name="type" required class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="saving">💾 Saving Plan</option>
                            <option value="tithe">🙏 Tithe Reminder</option>
                            <option value="bill">📋 Bill Payment</option>
                            <option value="investment">📈 Investment</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Title</label>
                        <input type="text" name="title" required placeholder="e.g., Emergency Fund, Rent, Tithe..." class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Target Amount ($)</label>
                        <input type="number" step="0.01" name="amount" class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="0.00" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Frequency</label>
                        <select name="frequency" class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="one-time">One-time</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="annually">Annually</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Due Date</label>
                        <input type="date" name="due_date" class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Reminder (days before)</label>
                        <input type="number" name="reminder_days" value="3" min="0" class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-300">Notes</label>
                    <textarea name="description" rows="2" placeholder="Add any additional notes..." class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>
                
                <button type="submit" class="mt-4 rounded-2xl bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-400 transition">
                    Create Plan
                </button>
            </form>
        </section>

        <!-- Financial Plans List -->
        <section class="space-y-4">
            @php
                $filter = request('filter');
                $filteredFinancials = $filter ? $financials->where('type', $filter) : $financials;
            @endphp

            @if($filteredFinancials->count() > 0)
                @foreach($filteredFinancials as $financial)
                    <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg {{ $financial->is_completed ? 'opacity-60' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    @if($financial->type === 'saving')
                                        <span class="text-emerald-400">💰</span>
                                        <p class="font-semibold text-white">Saving Plan: {{ $financial->title }}</p>
                                    @elseif($financial->type === 'tithe')
                                        <span class="text-amber-400">🙏</span>
                                        <p class="font-semibold text-white">Tithe: {{ $financial->title }}</p>
                                    @elseif($financial->type === 'bill')
                                        <span class="text-rose-400">📋</span>
                                        <p class="font-semibold text-white">Bill: {{ $financial->title }}</p>
                                    @elseif($financial->type === 'investment')
                                        <span class="text-sky-400">📈</span>
                                        <p class="font-semibold text-white">Investment: {{ $financial->title }}</p>
                                    @endif
                                </div>
                                
                                @if($financial->description)
                                    <p class="mt-1 text-sm text-slate-400">{{ $financial->description }}</p>
                                @endif
                                
                                <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-400">
                                    @if($financial->amount)
                                        <span class="rounded-full bg-slate-800 px-3 py-1">Target: ${{ number_format($financial->amount, 2) }}</span>
                                    @endif
                                    @if($financial->frequency)
                                        <span class="rounded-full bg-slate-800 px-3 py-1">{{ ucfirst($financial->frequency) }}</span>
                                    @endif
                                    @if($financial->due_date)
                                        <span class="rounded-full bg-slate-800 px-3 py-1">Due: {{ \Carbon\Carbon::parse($financial->due_date)->format('M d, Y') }}</span>
                                    @endif
                                    @if($financial->reminder_days)
                                        <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-indigo-300">Reminder: {{ $financial->reminder_days }} days before</span>
                                    @endif
                                </div>

                                @if($financial->type === 'bill')
                                    <div class="mt-3">
                                        <form method="POST" action="{{ route('financials.toggle', $financial) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-2 text-xs font-medium text-emerald-300 hover:bg-emerald-500/20 transition">
                                                {{ $financial->is_completed ? '✓ Marked as Paid' : '✓ Mark as Paid' }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('financials.toggle', $financial) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded-xl border border-white/10 bg-slate-800 p-2 text-slate-300 hover:text-white transition" title="Toggle completion">
                                        {{ $financial->is_completed ? '↩️' : '✅' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('financials.destroy', $financial) }}" class="inline" onsubmit="return confirm('Delete this plan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl border border-rose-500/20 bg-slate-800 p-2 text-rose-400 hover:text-rose-300 transition" title="Delete">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="rounded-2xl border border-dashed border-white/10 p-8 text-center">
                    <p class="text-slate-400">No financial plans yet. Start planning above.</p>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>