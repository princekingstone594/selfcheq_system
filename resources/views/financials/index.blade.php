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
                    <p class="text-sm italic text-emerald-100">"Every wealth journey begins with a single step. Start today, stay consistent, and watch your dreams become reality."</p>
                </div>
            </div>
        </section>

        <!-- Bills & Payments Section -->
        <section class="rounded-3xl border border-rose-400/20 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-2xl">📋</span>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-rose-300">Bills & Payments</p>
                    <p class="text-xs text-slate-400">Track and manage your bills</p>
                </div>
            </div>

            <!-- Add Bill Form -->
            <form method="POST" action="{{ route('financials.store') }}" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="type" value="bill" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-300">Bill Name</label>
                        <input type="text" name="title" required placeholder="e.g., Rent, Electricity, Internet..." 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-rose-500 focus:ring-1 focus:ring-rose-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Amount ($)</label>
                        <input type="number" step="0.01" name="amount" placeholder="0.00" 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-rose-500 focus:ring-1 focus:ring-rose-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Due Date</label>
                        <input type="date" name="due_date" required 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-rose-500 focus:ring-1 focus:ring-rose-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Reminder (days before)</label>
                        <input type="number" name="reminder_days" value="3" min="0" 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-rose-500 focus:ring-1 focus:ring-rose-500" />
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-2xl bg-rose-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-rose-400 transition">
                            + Add Bill
                        </button>
                    </div>
                </div>
            </form>

            <!-- Bills List -->
            @if($bills->count() > 0)
                <div class="mt-6 space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Your Bills</p>
                    @foreach($bills as $bill)
                        <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4 {{ $bill->is_completed ? 'opacity-60' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-rose-400">📋</span>
                                        <p class="font-semibold text-white">{{ $bill->title }}</p>
                                    </div>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-400">
                                        @if($bill->amount)
                                            <span class="rounded-full bg-slate-700 px-3 py-1">${{ number_format($bill->amount, 2) }}</span>
                                        @endif
                                        @if($bill->due_date)
                                            <span class="rounded-full bg-slate-700 px-3 py-1">Due: {{ \Carbon\Carbon::parse($bill->due_date)->format('M d, Y') }}</span>
                                        @endif
                                        @if($bill->reminder_days)
                                            <span class="rounded-full bg-rose-500/20 px-3 py-1 text-rose-300">Reminder: {{ $bill->reminder_days }} days before</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('financials.toggle', $bill) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-3 py-2 text-xs font-medium text-emerald-300 hover:bg-emerald-500/20 transition">
                                            {{ $bill->is_completed ? '✓ Paid' : '✓ Mark Paid' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('financials.destroy', $bill) }}" class="inline" onsubmit="return confirm('Delete this bill?')">
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
                </div>
            @else
                <div class="mt-4 rounded-2xl border border-dashed border-white/10 p-6 text-center">
                    <p class="text-sm text-slate-400">No bills yet. Add your first bill above.</p>
                </div>
            @endif
        </section>

        <!-- Tithe Reminders Section -->
        <section class="rounded-3xl border border-amber-400/20 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-2xl">🙏</span>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300">Tithe Reminders</p>
                    <p class="text-xs text-slate-400">Set reminders for your tithe payments</p>
                </div>
            </div>

            <!-- Add Tithe Reminder Form -->
            <form method="POST" action="{{ route('financials.store') }}" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="type" value="tithe" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-300">Tithe Description (optional)</label>
                        <input type="text" name="title" placeholder="e.g., Weekly Tithe, Monthly Offering..." 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Payment Day</label>
                        <select name="due_date" required class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">Select day of month</option>
                            @for($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Frequency</label>
                        <select name="frequency" required class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Reminder (days before)</label>
                        <input type="number" name="reminder_days" value="3" min="0" max="30" 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" />
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-2xl bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-400 transition">
                            + Add Tithe Reminder
                        </button>
                    </div>
                </div>
            </form>

            <!-- Tithe Reminders List -->
            @if($tithers->count() > 0)
                <div class="mt-6 space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Active Tithe Reminders</p>
                    @foreach($tithers as $tithe)
                        <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4 {{ $tithe->is_completed ? 'opacity-60' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-amber-400">🙏</span>
                                        <p class="font-semibold text-white">{{ $tithe->title ?: 'Tithe Payment' }}</p>
                                    </div>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-400">
                                        <span class="rounded-full bg-amber-500/20 px-3 py-1 text-amber-300">Day {{ $tithe->due_date }} of month</span>
                                        <span class="rounded-full bg-slate-700 px-3 py-1">{{ ucfirst($tithe->frequency) }}</span>
                                        @if($tithe->reminder_days)
                                            <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-indigo-300">Reminder: {{ $tithe->reminder_days }} days before</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('financials.toggle', $tithe) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-3 py-2 text-xs font-medium text-emerald-300 hover:bg-emerald-500/20 transition">
                                            {{ $tithe->is_completed ? '✓ Paid' : '✓ Mark Paid' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('financials.destroy', $tithe) }}" class="inline" onsubmit="return confirm('Delete this reminder?')">
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
                </div>
            @else
                <div class="mt-4 rounded-2xl border border-dashed border-white/10 p-6 text-center">
                    <p class="text-sm text-slate-400">No tithe reminders set. Add one above to stay on track with your giving.</p>
                </div>
            @endif
        </section>

        <!-- Savings Plan Section -->
        <section class="rounded-3xl border border-emerald-400/20 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-2xl">💰</span>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-300">Saving Plans</p>
                    <p class="text-xs text-slate-400">Set and track your savings goals</p>
                </div>
            </div>

            <!-- Add Savings Plan Form -->
            <form method="POST" action="{{ route('financials.store') }}" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="type" value="saving" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-300">Savings Goal Title</label>
                        <input type="text" name="title" required placeholder="e.g., Emergency Fund, Vacation, New Car..." 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Target Amount ($)</label>
                        <input type="number" step="0.01" name="amount" placeholder="0.00" 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
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
                        <label class="block text-sm font-medium text-slate-300">Target Date</label>
                        <input type="date" name="due_date" 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Reminder (days before)</label>
                        <input type="number" name="reminder_days" value="3" min="0" 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-300">Notes</label>
                        <textarea name="description" rows="2" placeholder="Add any additional notes..." 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full rounded-2xl bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-400 transition">
                            + Create Savings Plan
                        </button>
                    </div>
                </div>
            </form>

            <!-- Savings Plans List -->
            @if($savings->count() > 0)
                <div class="mt-6 space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Your Savings Plans</p>
                    @foreach($savings as $saving)
                        <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4 {{ $saving->is_completed ? 'opacity-60' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-emerald-400">💰</span>
                                        <p class="font-semibold text-white">{{ $saving->title }}</p>
                                    </div>
                                    @if($saving->description)
                                        <p class="mt-1 text-sm text-slate-400">{{ $saving->description }}</p>
                                    @endif
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-400">
                                        @if($saving->amount)
                                            <span class="rounded-full bg-slate-700 px-3 py-1">Target: ${{ number_format($saving->amount, 2) }}</span>
                                        @endif
                                        @if($saving->frequency)
                                            <span class="rounded-full bg-slate-700 px-3 py-1">{{ ucfirst($saving->frequency) }}</span>
                                        @endif
                                        @if($saving->due_date)
                                            <span class="rounded-full bg-slate-700 px-3 py-1">Due: {{ \Carbon\Carbon::parse($saving->due_date)->format('M d, Y') }}</span>
                                        @endif
                                        @if($saving->reminder_days)
                                            <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-indigo-300">Reminder: {{ $saving->reminder_days }} days before</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('financials.toggle', $saving) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="rounded-xl border border-white/10 bg-slate-800 p-2 text-slate-300 hover:text-white transition" title="Toggle completion">
                                            {{ $saving->is_completed ? '↩️' : '✅' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('financials.destroy', $saving) }}" class="inline" onsubmit="return confirm('Delete this plan?')">
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
                </div>
            @else
                <div class="mt-4 rounded-2xl border border-dashed border-white/10 p-6 text-center">
                    <p class="text-sm text-slate-400">No savings plans yet. Create one above to start building your future.</p>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>